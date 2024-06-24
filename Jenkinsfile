pipeline {
  agent any
  stages {
    stage('Checkout SCM') {
      steps {
        git(url: 'https://github.com/Ninjarku/WhatTheDuck', branch: 'main', credentialsId: 'juan-pound-fish')
      }
    }
  
     stage('Copy Files to Container') {
      steps {
        script {
          // List files in the workspace to verify they are there
          sh 'ls -la $WORKSPACE'
          
          // Copy files to the container
          sh 'docker cp $WORKSPACE/. php-docker:/var/www/html/'
          
          // List files in the container to verify the copy worked
          sh 'docker exec php-docker ls -la /var/www/html'
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
