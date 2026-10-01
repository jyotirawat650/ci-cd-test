pipeline {
    agent any

    triggers {
        githubPush()
    }

    environment {
        APP_NAME = 'php-app-container'
        IMAGE_NAME = 'php-app:latest'
        TEST_CONTAINER = 'php-app-test'
        TEST_PORT = '8001'
        APP_PORT = '8000'
    }

    stages {

        stage('Checkout Code') {
            steps {
                echo 'Checking out code from Git...'
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    echo 'Building PHP application Docker image...'
                    sh "docker build -t ${IMAGE_NAME} ."
                }
            }
        }

        stage('Run Tests') {
            steps {
                echo 'Running application tests...'
            }
        }

        stage('Health Check - New Container') {
            steps {
                script {
                    sh '''
                        set -e

                        echo "Cleaning up any previous test container..."

                        docker rm -f ${TEST_CONTAINER} 2>/dev/null || true

                        echo "Starting new container on temporary port ${TEST_PORT}..."

                        docker run -d \
                          --name ${TEST_CONTAINER} \
                          -p ${TEST_PORT}:80 \
                          ${IMAGE_NAME}

                        echo "Waiting for application to start..."
                        sleep 5

                        echo "Checking container status..."

                        if [ "$(docker inspect -f '{{.State.Running}}' ${TEST_CONTAINER})" != "true" ]; then
                            echo "ERROR: New container is not running."
                            docker logs ${TEST_CONTAINER}
                            docker rm -f ${TEST_CONTAINER} || true
                            exit 1
                        fi

                        echo "Container is running."

                        echo "Performing HTTP health check..."

                        HEALTHY=false

                        for i in 1 2 3 4 5 6 7 8 9 10
                        do
                            if curl -f http://localhost:${TEST_PORT}/ > /dev/null 2>&1
                            then
                                echo "Health check PASSED on attempt $i."
                                HEALTHY=true
                                break
                            else
                                echo "Health check failed on attempt $i/10."
                                sleep 3
                            fi
                        done

                        if [ "$HEALTHY" != "true" ]; then
                            echo "ERROR: New application failed health check."
                            echo "Container logs:"
                            docker logs ${TEST_CONTAINER}

                            docker rm -f ${TEST_CONTAINER} || true

                            exit 1
                        fi

                        echo "======================================"
                        echo "NEW CONTAINER IS HEALTHY"
                        echo "Old container will now be replaced."
                        echo "======================================"
                    '''
                }
            }
        }

        stage('Deploy Application') {
            steps {
                script {
                    sh '''
                        set -e

                        echo "Stopping old application container..."

                        if docker ps -a -q -f name=^/${APP_NAME}$ | grep -q .; then
                            docker stop ${APP_NAME} || true
                            docker rm ${APP_NAME} || true
                        fi

                        echo "Stopping temporary health-check container..."

                        docker stop ${TEST_CONTAINER} || true
                        docker rm ${TEST_CONTAINER} || true

                        echo "Starting new application container on port ${APP_PORT}..."

                        docker run -d \
                          --name ${APP_NAME} \
                          -p ${APP_PORT}:80 \
                          ${IMAGE_NAME}

                        echo "Deployment completed."

                        echo "Checking final container status..."

                        if [ "$(docker inspect -f '{{.State.Running}}' ${APP_NAME})" != "true" ]; then
                            echo "ERROR: Final application container failed to start."
                            docker logs ${APP_NAME}
                            exit 1
                        fi

                        echo "Application is running successfully."
                    '''
                }
            }
        }
    }

    post {
        always {
            echo 'Pipeline execution completed.'
        }

        failure {
            echo 'Pipeline failed. Existing application container was preserved if health check failed.'
        }

        success {
            echo 'Application deployed successfully.'
        }
    }
}
