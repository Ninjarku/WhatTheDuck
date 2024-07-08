pipeline {
     agent any

     environment {
        TEST_USERNAME = credentials('UserTest')
        TEST_PASSWORD = credentials('UserTest')
        CHROME_BIN = "/usr/bin/google-chrome"
        CHROMEDRIVER_BIN = "/usr/local/bin/chromedriver"
    }

    stages {
        stage('Checkout SCM') {
            steps {
                git(url: 'https://github.com/Ninjarku/WhatTheDuck', branch: 'main', credentialsId: 'juan-pound-fish')
            }
        }
         
        stage('Static Code Analysis with SonarQube') {
            steps {
                script {
                    def scannerHome = tool 'SonarQube';
                    withSonarQubeEnv('SonarQube') {
                    sh "${scannerHome}/bin/sonar-scanner -Dsonar.projectKey=WhatTheDuck -Dsonar.sources=src"
                    }
                }
            }
        }

        stage('Security Analysis with OWASP Dependency-Check Plugin') {
             steps {
                withCredentials([string(credentialsId: 'nvd_api_key', variable: 'nvd_api_key')]) {
                    dependencyCheck additionalArguments: "--scan src --format HTML --format XML --nvdApiKey ${env.nvd_api_key}", odcInstallation: 'OWASP Dependency-Check Vulnerabilities'
                }
            }
        }

          

          stage('Unit Testing with PHPUnit') {
           steps {
               script {
                    sh 'docker exec -i php-docker ./vendor/bin/phpunit --log-junit /var/www/html/test-results/unitreport.xml -c /var/www/private/tests/unit/phpunit.xml /var/www/private/tests/unit'
               
               }
           }
       }
    
         stage('Deployment to AWS Web Server') {
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
                                verbose: true
                            )
                        ]
                    )
                }
            }
        }
        //   stage('UI Tests with Selenium') {
        //     steps {
        //         withCredentials([usernamePassword(credentialsId: 'UserTest', usernameVariable: 'TEST_USERNAME', passwordVariable: 'TEST_PASSWORD')]) {
        //             script {
        //                 try {
        //                     // Run Selenium tests
        //                     sh 'export PATH=$PATH:/usr/local/bin'
        //                     sh 'mvn test'
        //                 } catch (Exception e) {
        //                     echo "Selenium tests failed. Rolling back deployment..."
        //                     // Revert to the previous commit
        //                     sh 'git reset --hard HEAD~1'
        //                     // Redeploy the previous version
        //                     sshPublisher(
        //                         publishers: [
        //                             sshPublisherDesc(
        //                                 configName: 'jenkins ssh',
        //                                 transfers: [
        //                                     sshTransfer(
        //                                         sourceFiles: 'src/**/*', // Use wildcard to match all files in src directory
        //                                         removePrefix: 'src' // Remove src prefix
        //                                     )
        //                                 ],
        //                                 verbose: true
        //                             )
        //                         ]
        //                     )
        //                     throw e // Re-throw the exception to mark the build as failed
        //                 }
        //             }
        //         }
        //     }
        // }
     
         
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
