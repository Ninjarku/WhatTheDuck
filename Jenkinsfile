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
        sh 'docker exec php-docker composer install'
      }
    }
    stage('Ensure PHP Docker is Running') {
      steps {
        script {
          // Check if the container is running, if not, start it
          def containerExists = sh(script: "docker ps -q -f name=php-docker", returnStdout: true).trim()
          if (!containerExists) {
            sh 'docker start php-docker || docker run -d --name php-docker --network jenkins -p 80:80 -v ~/docker-volumes/php-docker:/var/www/html php-docker'
          }
        }
      }
    }
    stage('Build') {
      steps {
        sh 'docker exec php-docker composer install'
      }
    }
    stage('Test') {
      steps {
        sh 'docker exec php-docker ./vendor/bin/phpunit --configuration /var/www/html/tests/phpunit.xml'
      }
    }
    stage('Sync Files') {
      steps {
        sh 'rsync -av --exclude=\'vendor/\' ./ /path/to/your/container/volume/'
      }
    }
  }
  post {
    always {
      junit 'tests/reports/phpunit.xml' // Ensure this matches the path in phpunit.xml
    }
  }
    success {
      echo 'Pipeline completed successfully.'
    }
    failure {
      echo 'Pipeline failed.'
    }
  }
}
