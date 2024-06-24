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

    stage('Deploy') {
      steps {
        script {
          // Stop and remove the old container, then start a new one
          sh 'docker stop php-docker || true && docker rm php-docker || true'
          sh 'docker run -d --name php-docker --network jenkins -p 80:80 -v ~/docker-volumes/php-docker:/var/www/html php-docker'
          
          // Optionally restart Nginx to apply new configurations if needed
          sh 'docker restart nginx || true'
        }
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
