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
    stage('Test') {
      steps {
        sh 'docker exec php-docker ./vendor/bin/phpunit --configuration /var/www/html/tests/phpunit.xml'
      }
    }
    stage('Sync Files') {
      steps {
        script {
          sh 'docker cp . ~/docker-volumes/php-docker:/var/www/html/'
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
