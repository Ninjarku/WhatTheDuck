pipeline {
  agent any
  stages {
    stage('Checkout SCM') {
      steps {
        git(url: 'https://github.com/Ninjarku/WhatTheDuck', branch: 'main', credentialsId: 'juan-pound-fish')
      }
    }
    stage('Build Docker Image') {
      steps {
        script {
          // Build the docker image
          sh 'docker build -t php-docker .'
        }
      }
    }

   stage('Run Tests') {
      steps {
        script {
          // Run PHPUnit tests inside the container
           sh 'docker exec php-docker ./vendor/bin/phpunit --configuration /var/www/html/tests/phpunit.xml'
        }
      }
    }

     stage('Sync Files') {
      steps {
        sh 'rsync -av --exclude=\'vendor/\' ./ ~/docker-volumes/php-docker/'
      }
    }
  }
  post {
    always {
      junit 'tests/reports/phpunit.xml' // Ensure this matches the path in phpunit.xml
    }
    success {
      echo 'Pipeline completed successfully.'
    }
    failure {
      echo 'Pipeline failed.'
    }
  }
}
