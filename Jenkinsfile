pipeline {
     agent any

     environment {
        TEST_USERNAME = credentials('UserTest')
        TEST_PASSWORD = credentials('UserTest')
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
         
        stage('OWASP Dependency-Check Vulnerabilities') {
             steps {
                withCredentials([string(credentialsId: 'nvd_api_key', variable: 'nvd_api_key')]) {
                    dependencyCheck additionalArguments: "--scan src --format HTML --format XML --nvdApiKey ${env.nvd_api_key}", odcInstallation: 'OWASP Dependency-Check Vulnerabilities'
                }
                 // dependencyCheck additionalArguments: "--scan src --format HTML --format XML", odcInstallation: 'OWASP Dependency-Check Vulnerabilities'
            }
        }

          

          stage('PHPUnit Test') {
           steps {
                // withCredentials([usernamePassword(credentialsId: 'UserTest', usernameVariable: 'TEST_USERNAME', passwordVariable: 'TEST_PASSWORD')]) {
               script {

                    sh 'docker exec -i php-docker ./vendor/bin/phpunit --log-junit /var/www/html/test-results/unitreport.xml -c /var/www/private/tests/unit/phpunit.xml /var/www/private/tests/unit'
               
               }
              // }
           }
       }

         stage('Run Selenium Tests') {
            steps {
                script {
                    sh 'mvn test'
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
          // junit '/var/www/html/test-results/unitreport.xml'
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
