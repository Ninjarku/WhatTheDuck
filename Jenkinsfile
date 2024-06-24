pipeline {
  agent any
  stages {
    stage('Checkout SCM') {
      steps {
        git(url: 'https://github.com/Ninjarku/WhatTheDuck', branch: 'main', credentialsId: 'juan-pound-fish')
      }
    }
  
     stage('Copy Files to Volume') {
      steps {
        script {
          // Copy files to the volume
          sh "docker cp $WORKSPACE/. $(docker container ls -qf name=php-docker):/var/www/html"
          
          // List files in the volume to verify the copy worked
          sh 'ls -la ~/docker-volumes/php-docker/'
        }
      }
    }
  }
  
  post {
    success {
      echo 'Pipeline completed successfully.'
    }
    failure {
      echo 'Pipeline failed.'
    }
  }
    
    
  }
