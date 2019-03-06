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
        ssh dpdshipping@demo.adeoweb.biz "cd /home/dpdshipping/magento2-3/www/magento2 && bin/magento setup:upgrade && bin/magento deploy:mode:set production"
        '''
      }
    }   
  }
}
