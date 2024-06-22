pipeline {
  agent any
  stages {
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
