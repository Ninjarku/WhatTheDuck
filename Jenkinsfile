pipeline {
    agent any

    stages {
        stage('Checkout SCM') {
            steps {
                git(url: 'https://github.com/Ninjarku/WhatTheDuck', branch: 'main', credentialsId: 'juan-pound-fish')
            }
        }
	stage('Build') {
            agent {
                docker {
                    image 'composer:latest'
                }
            }
            steps {
                sh 'composer install'
            }
        }
	stage('Test') {
			steps {
                sh './vendor/bin/phpunit tests/unit'
            }
		}

    }
}
