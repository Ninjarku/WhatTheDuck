pipeline {
  agent any
  stages {
    stage('Build') {
      steps {
        sh 'pip install -r requirements.txt'
      }
    }

    stage('Testing') {
      steps {
        sh 'robot tests/tests.txt'
      }
    }

    stage('Reporting') {
      steps {
        archiveArtifacts 'report.html,log.html,output.xml'
      }
    }

  }
}