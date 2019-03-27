//DPD Magento Module Deployment
pipeline {
  agent any
  stages {
    stage('Update Modules') {
      when {
          branch 'master'
      }
      steps {
        sh '''
        ssh dpdshipping@demo.adeoweb.biz "cd /home/dpdshipping/magento2-3/www/magento2 && composer update && bin/magento setup:upgrade && bin/magento deploy:mode:set production"
        ssh dpdshipping@demo.adeoweb.biz "cd /home/dpdshipping/ && ./cachetool.phar opcache:reset"
        '''
      }
    }   
  }
}
