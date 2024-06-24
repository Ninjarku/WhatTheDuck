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
                   // Get the container ID or name dynamically
                    def containerId = sh(script: "docker container ls -qf name=${DOCKER_CONTAINER}", returnStdout: true).trim()
                    
                    // Copy files from Jenkins workspace to the Docker container
                    sh "docker cp ${WORKSPACE}/. ${containerId}:/var/www/html"
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
