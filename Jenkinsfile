pipeline {
     agent any
    
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


        stage('PHPUnit Test') {
            steps {
                script {
                    
                        sh 'phpunit --log-junit logs/unitreport.xml -c phpunit.xml tests/unit'
                
                }
            }
        }
        stage('OWASP Dependency-Check Vulnerabilities') {
            steps {
                script {
                   dependencyCheck additionalArguments: '--scan src --format HTML --format XML --nvdApiKey f9133a9c-4161-4bb6-846e-2aace202bb80', odcInstallation: 'OWASP Dependency-Check Vulnerabilities'
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
                                        sourceFiles: 'src/**/*', // Use wildcard to match all files in src directory
                                        removePrefix: 'src', // Remove src prefix
                
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
            dependencyCheckPublisher pattern: 'dependency-check-report.xml'
        }
        success {
            echo "Pipline Success!"
        }
        failure {
            echo "Pipline Failed!"
        }
    }
    
}
