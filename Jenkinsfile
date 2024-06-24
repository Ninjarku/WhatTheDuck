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
                   sh '''
                    echo "Current working directory:"
                    pwd

                    # Copy the files to the target directory
                    docker cp -r * /home/student9/docker-volumes/php-docker

  
                    '''
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
