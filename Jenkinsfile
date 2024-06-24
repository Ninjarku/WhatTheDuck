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

                    # Determine the Jenkins workspace directory
                    WORKSPACE=$(pwd)

                    # Copy files from the Jenkins workspace to the host
                    sudo docker cp $WORKSPACE /home/student9/docker-volumes/php-docker

  
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
