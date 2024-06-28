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
     stage('Check Changes') {
          when {
                branch 'main'
            }
            steps {
                script {
                    def changeLogSets = currentBuild.changeSets
                    def foundChange = false

                    for (int i = 0; i < changeLogSets.size(); i++) {
                        def entries = changeLogSets[i].items
                        for (int j = 0; j < entries.length; j++) {
                            def files = entries[j].affectedFiles
                            for (int k = 0; k < files.size(); k++) {
                                def file = files[k]
                                if (file.path.startsWith('src/')) {
                                    foundChange = true
                                    break
                                }
                            }
                            if (foundChange) {
                                break
                            }
                        }
                        if (foundChange) {
                            break
                        }
                    }

                    if (!foundChange) {
                        echo "No changes in the specified folder. Skipping build."
                        currentBuild.result = 'SUCCESS'
                        error("No changes in the specified folder.")
                    }
                }
            }
        }
        stage('Build') {
            when {
                expression {
                    return currentBuild.result == null
                }
            }
            steps {
                script {
                    sh 'composer install'
                }
            }
        }


        // stage('PHPUnit Test') {
        //     steps {
        //         script {
                    
        //                 sh 'phpunit --log-junit logs/unitreport.xml -c phpunit.xml tests/unit'
                
        //         }
        //     }
        // }
         
        stage('OWASP Dependency-Check Vulnerabilities') {
            steps {
                script {
                   dependencyCheck additionalArguments: '--scan src --format HTML --format XML', odcInstallation: 'OWASP Dependency-Check Vulnerabilities'
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
