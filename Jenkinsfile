pipeline {
    agent any

    stages {
        stage('Checkout SCM') {
            steps {
                git(url: 'https://github.com/Ninjarku/WhatTheDuck', branch: 'main', credentialsId: 'juan-pound-fish')
            }
        }

        stage('Install Dependencies') {
            steps {
                script {
                    // Install necessary PHP extensions
                    sh 'apt-get update && apt-get install -y libxml2-dev'
                    sh 'docker-php-ext-install simplexml dom'
                    
                    // Install Composer dependencies
                    sh 'composer install'
                }
            }
        }

        stage('Static Code Analysis') {
            steps {
                script {
                    sh 'vendor/bin/phpcs --standard=PSR12 src'
                }
            }
        }

        stage('Unit Testing') {
            steps {
                script {
                    sh 'vendor/bin/phpunit --coverage-clover coverage.xml'
                }
                junit 'tests/**/*.xml'
            }
        }

        stage('Security Testing') {
            steps {
                script {
                    sh 'vendor/bin/phpstan analyse --level=max src'
                }
            }
        }
    }

    post {
        always {
            archiveArtifacts artifacts: '**/coverage.xml', allowEmptyArchive: true
            junit '**/test-results/**/*.xml'
        }
        success {
            echo 'Pipeline succeeded!'
        }
        failure {
            echo 'Pipeline failed!'
        }
    }
}
