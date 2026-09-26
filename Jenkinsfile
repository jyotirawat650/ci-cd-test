pipeline {
    agent any

    environment {
        APP_NAME = 'php-app-container'
        IMAGE_NAME = 'php-app:latest'
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

        stage('Deploy Application') {
            steps {
                script {
                    echo 'Redeploying application container...'
                    sh '''
                        # Stop and remove existing container if running
                        if [ $(docker ps -a -q -f name=^/${APP_NAME}$) ]; then
                            docker stop ${APP_NAME} || true
                            docker rm ${APP_NAME} || true
                        fi

                        # Run new container connected to host port 8000
                        docker run -d \
                          --name ${APP_NAME} \
                          -p 8000:80 \
                          ${IMAGE_NAME}
                    '''
                }
            }
        }
    }
}
