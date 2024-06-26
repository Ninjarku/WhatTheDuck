pipeline {
    agent {
        docker {
            image 'composer:latest'
        }
    }

     environment {
        DEPLOY_PATH = "/home/student9/docker-volumes/php-docker/whattheduck"  // Path on your AWS instance
    }

    stages {
        stage('Checkout SCM') {
            steps {
                git(url: 'https://github.com/Ninjarku/WhatTheDuck', branch: 'main', credentialsId: 'juan-pound-fish')
            }
        }

        stage('Build') {
            steps {
                script {
           
                        sh 'composer install'
                    
                }
            }
        }

        stage('Test') {
            steps {
                script {
                    
                        sh './vendor/bin/phpunit --log-junit logs/unitreport.xml -c phpunit.xml tests/unit'
                
                }
            }
        }
        stage('Check Node') {
            steps {
                script {
                    echo "Running on node: ${env.NODE_NAME}"
                }
            }
        }
          stage('Deploy') {
            steps {
                script {
                    sshPublisher(
                        publishers: [
                            sshPublisherDesc(
                                configName: 'jenkins ssh',
                                transfers: [
                                    sshTransfer(
                                        sourceFiles: 'src/*',
                                        removePrefix: 'src/',
                                        remoteDirectory: '/home/student9/docker-volumes/php-docker/whattheduck',
                                        execCommand: ''
                                    )
                                ],
                                usePromotionTimestamp: false,
                                useWorkspaceInPromotion: false,
                                verbose: true
                            )
                        ]
                    )
                }
            }
        }
    }
    post {
        always {
            junit testResults: 'logs/unitreport.xml'
        }
    }
}
