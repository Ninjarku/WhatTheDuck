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

        stage('PHPUnit Test') {
            steps {
                script {
                    
                        sh './vendor/bin/phpunit --log-junit logs/unitreport.xml -c phpunit.xml tests/unit'
                
                }
            }
        }
        stage('OWASP Dependency-Check Vulnerabilities') {
            steps {
                script {
                    def dependencyCheckHome = tool name: 'OWASP_Dependency-Check'
                    sh "${dependencyCheckHome}/bin/dependency-check.sh --project WhatTheDuck --scan . --format ALL --out dependency-check-report.xml --prettyPrint"
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
