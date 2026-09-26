pipeline {
    agent any
    stages {
        stage('Checkout Code') {
            steps {
                echo 'Checking out code from Git...'
            }
        }
        stage('Build Docker Image') {
            steps {
                script {
                    echo 'Building PHP application Docker image...'
                    sh 'docker build -t php-app:latest .'
                }
            }
        }
        stage('Run Tests') {
            steps {
                echo 'Running application tests...'
            }
        }
    }
}