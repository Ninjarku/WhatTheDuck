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
                    // Copy files from Jenkins workspace to the Docker container
                    sh "docker cp ${WORKSPACE}/. $(docker container ls -qf name=php-docker):/var/www/html"
                    
                    // List files in the container directory to verify the copy worked
                    sh 'docker exec $(docker container ls -qf name=php-docker) ls -la /var/www/html'
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
