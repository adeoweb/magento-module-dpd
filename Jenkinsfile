pipeline {
  agent {
    docker {
      image 'awh0/php:72.1'
      args "-v /var/lib/jenkins/composer/dpd-module:/.composer"
    }
  }
  environment {
      COMPOSER_REPO = credentials('composer-user')
  }
  stages {
    stage('Build') {
      steps {
        sh "composer config --global repositories.magento composer https://repo.magento.com"
        sh "composer config http-basic.repo.magento.com $COMPOSER_REPO_USR $COMPOSER_REPO_PSW"
        sh "composer install --no-progress --optimize-autoloader"
      }
    }
    stage('Test unit') {
      steps {
        sh 'mkdir results'
        sh './vendor/bin/phpunit --log-junit=results/phpunit.xml'
      }
    }
  }
  post {
    always {
      junit '**/results/*.xml'
    }
  }
}
