pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
                sh 'composer install'
            }
        }
        stage('Test') {
            steps {
                sh 'phpunit'
            }
        }
        stage('Deploy') {
            steps {
                // Example deployment step, adjust as per your setup
                sh 'rsync -avz --delete ./ ~/docker-volumes/php-docker:/var/www/html'
            }
        }
    }
}
