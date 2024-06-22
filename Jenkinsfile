pipeline {
    agent any

    stages {
        stage('Build') {
            steps {
                // Optionally run any build steps for your PHP application
                sh 'composer install'  // Example: Install PHP dependencies
            }
        }
        stage('Test') {
            steps {
                // Optionally run tests for your PHP application
                sh 'phpunit'  // Example: Run PHPUnit tests
            }
        }
        stage('Deploy') {
            steps {
                // Copy PHP, CSS, and JS files to the running Apache container
                sh 'docker cp ./ ~/docker-volumes/php-docker:/var/www/html'
            }
        }
    }
}
