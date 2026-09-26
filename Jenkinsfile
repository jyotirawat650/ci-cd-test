pipeline {
    agent any

    triggers {
        githubPush()
    }

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
                        if [ $(docker ps -a -q -f name=^/${APP_NAME}$) ]; then
                            docker stop ${APP_NAME} || true
                            docker rm ${APP_NAME} || true
                        fi

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
