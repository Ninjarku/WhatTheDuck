pipeline {
    agent any

    stages {
        stage('Checkout SCM') {
            steps {
                git(url: 'https://github.com/Ninjarku/WhatTheDuck', branch: 'main', credentialsId: 'juan-pound-fish')
            }
        }

        stage('Build') {
            steps {
                script {
                    docker.image('composer:latest').inside {
                        sh 'composer install'
                    }
                }
            }
        }

        stage('Test') {
            steps {
                script {
                    docker.image('composer:latest').inside {
                        sh './vendor/bin/phpunit tests/unit'
                    }
                }
            }
        }
    }
}
