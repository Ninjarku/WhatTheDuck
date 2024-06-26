pipeline {
    agent {
        docker {
            image 'composer:latest'
        }
    }

     environment {
        DEPLOY_PATH = '~/docker-volumes/php-docker/whattheduck'  // Path on your AWS instance
    }

    stages {
        stage('Checkout SCM') {
            steps {
                git(url: 'https://github.com/Ninjarku/WhatTheDuck', branch: 'main', credentialsId: 'juan-pound-fish')
            }
        }

        stage('Build') {
            steps {
                script {
           
                        sh 'composer install'
                    
                }
            }
        }

        stage('Test') {
            steps {
                script {
                    
                        sh './vendor/bin/phpunit --log-junit logs/unitreport.xml -c phpunit.xml tests/unit'
                
                }
            }
        }
         stage('Deploy') {
            steps {
                script {
                    sh '''
                        cp -r src/* $DEPLOY_PATH
                    '''
                }
            }
        }
    }
    post {
        always {
            junit testResults: 'logs/unitreport.xml'
        }
    }
}
