pipeline {
    agent {
        docker {
            image 'composer:latest'
        }
    }

     environment {
        DEPLOY_USER = 'student9'  // Change to your SSH username
        DEPLOY_HOST = '18.224.18.18'  // Change to your AWS instance IP
        DEPLOY_PATH = '/home/student9/docker-volumes/php-docker/whattheduck'  // Path on your AWS instance
        SSH_KEY = credentials('ssh')  // Jenkins credentials ID for your PEM key
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
                        scp -i $SSH_KEY -r src/* $DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_PATH
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
