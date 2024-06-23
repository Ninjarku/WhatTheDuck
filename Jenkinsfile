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
       
      }
    }

    stage('Test') {
      steps {
       
      }
    }

    stage('Deploy') {
      steps {
        sh 'docker cp ./ ~/docker-volumes/php-docker:/var/www/html'
      }
    }

  }
}
