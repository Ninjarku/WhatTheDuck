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

                    // Ensure the target directory exists and print the directory structure
                    mkdir -p ~/docker-volumes/php-docker
                    echo "Directory structure before copying files:"
                    ls -ld ~/docker-volumes
                    ls -ld ~/docker-volumes/php-docker

                    // Check directory path and permissions
                    if [ ! -d "~/docker-volumes/php-docker" ]; then
                        echo "Directory ~/docker-volumes/php-docker does not exist."
                        exit 1
                    fi

                    // Copy the files to the target directory
                    cp -r * ~/docker-volumes/php-docker

                    echo "Directory structure after copying files:"
                    ls -l ~/docker-volumes/php-docker
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
