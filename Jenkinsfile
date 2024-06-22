pipeline {
  agent any
  stages {
    stage('Build') {
      steps {
        sh 'composer install'
      }
    }

    stage('Test') {
      steps {
        sh 'phpunit'
      }
    }

    stage('Deploy') {
      steps {
        sh 'docker cp ./ ~/docker-volumes/php-docker:/var/www/html'
      }
    }

  }
}