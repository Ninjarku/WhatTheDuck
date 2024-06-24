pipeline {
  agent any
  stages {
    stage('Checkout SCM') {
      steps {
        git(url: 'https://github.com/Ninjarku/WhatTheDuck', branch: 'main', credentialsId: 'juan-pound-fish')
      }
    }
  
     stage('Sync Files') {
      steps {
        script {
        // Use rsync to copy files to the container
        sh 'rsync -avz --delete . ~/docker-volumes/php-docker'
        }
      }
    }
    

    stage('Install Dependencies') {
      steps {
        // Install composer dependencies inside the container
        sh 'docker exec php-docker composer install'
      }
    }
    
    stage('Test') {
      steps {
        sh 'docker exec php-docker ./vendor/bin/phpunit --configuration /var/www/html/tests/phpunit.xml'
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
