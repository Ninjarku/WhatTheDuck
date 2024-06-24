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

                    echo "Contents of the current working directory:"
                    ls -l

                    # Ensure the target directory exists
                    mkdir -p /home/student9/docker-volumes/php-docker

                    echo "Directory structure before copying files:"
                    ls -ld /home/student9/docker-volumes
                    ls -ld /home/student9/docker-volumes/php-docker

                    # Copy the files to the target directory
                    cp -r * /home/student9/docker-volumes/php-docker

                    echo "Directory structure after copying files:"
                    ls -l /home/student9/docker-volumes/php-docker
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
