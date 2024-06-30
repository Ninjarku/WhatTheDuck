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
         
        stage('Static Code Analysis') {
            steps {
                script {
                    // Run PHP CodeSniffer to generate a report
                    catchError(buildResult: 'UNSTABLE', stageResult: 'UNSTABLE') {
                        sh './vendor/bin/phpcs --standard=PSR12 src --report-file=phpcs.xml --report=checkstyle'
                    }
                }
            }
            post {
                always {
                    recordIssues(
                        tool: checkStyle(pattern: 'phpcs.xml'),
                        qualityGates: [[threshold: 1, type: 'TOTAL', unstable: true]]
                    )
                }
            }
        }

       

        stage('Code Quality Check via SonarQube') {
            steps {
                script {
                    def scannerHome = tool 'SonarQube';
                    withSonarQubeEnv('SonarQube') {
                    sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=WhatTheDuck -Dsonar.sources=src"
                    }
                }
            }
        }
         
        // stage('OWASP Dependency-Check Vulnerabilities') {
        //     steps {
        //         withCredentials([string(credentialsId: 'nvd_api_key', variable: 'nvd_api_key')]) {
        //             dependencyCheck additionalArguments: "--scan src --format HTML --format XML --nvdApiKey ${env.nvd_api_key}", odcInstallation: 'OWASP Dependency-Check Vulnerabilities'
        //         }
        //     }
        // }

          

          // stage('PHPUnit Test') {
          //  steps {
           //     script {
                    
            //            sh 'phpunit --log-junit logs/unitreport.xml -c phpunit.xml tests/unit'
                
            //    }
          //  }
       // }

        
    
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
        // always {
            //junit testResults: 'logs/unitreport.xml'
            //dependencyCheckPublisher pattern: 'dependency-check-report.xml'
       // }
        success {
            echo "Pipline Success!"
        }
        failure {
            echo "Pipline Failed!"
        }
    }
    
}
