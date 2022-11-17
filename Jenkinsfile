node {
    withEnv([
                'registry = 154265/laravel',
                'registryCredential = credential-docker',
                'dockerImage =null ',
                'MYSQL_ROOT_PASSWORD=password',
                'MYSQL_DATABASE=thetiptop',
                'MYSQL_DATABASE_TEST=thetiptoptest',
                'MYSQL_USER=thetiptop_user',
                'NAME_SERVER=server-mysql'
             ]) {

                 def remote = [:]
                remote.name = 'groupethe'
                remote.host = 'dsp-archiwebo20-mt-ma-ca-fd.fr'
                remote.user = 'groupethe'
                remote.password = 'projetThipTop20'
                remote.allowAnyHosts = true

                    stage('checkout') {
                        deleteDir()
                        checkout scm
                        echo 'Pulling...' + env.BRANCH_NAME
                    }


                     if(env.BRANCH_NAME =="develop") {

                        stage('Building our image for preprod') {
                            sh ' docker ps'
                            sh 'docker -v'
                            //dockerImage = docker.build "154265/laravel" + ":$BUILD_NUMBER"
                            dockerImage = docker.build "154265/laravel" + ":latest"
                         }

                         stage('Started container  and Run Unit and functionnal test in preprod ') {
                             container =  docker.image('mysql:5.7').withRun('-e "MYSQL_DATABASE=${MYSQL_DATABASE_TEST}" -e "MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}" -e "MYSQL_PASSWORD=${MYSQL_ROOT_PASSWORD}" -e "MYSQL_USER=${MYSQL_USER}" --name server-mysql -p 3306:3306 ') { c ->
                            docker.image('mysql:5.7').inside("--link ${c.id}:db") {
                            sh 'while ! mysqladmin ping -hdb --silent; do sleep 1; done'
                         }

                      containerLaravel =  dockerImage.inside("--link ${c.id}:db") {
                            sh 'composer install'
                            sh 'cp .env.example .env'
                            sh 'cat  .env'
                            sh 'php artisan key:generate'
                            sh "sed -i -e 's/DB_DATABASE=thetiptop/DB_DATABASE=thetiptoptest/g' .env"
                            sh "sed -i -e 's/DB_USERNAME=root/DB_USERNAME=thetiptop_user/g' .env"
                            sh "sed -i -e 's/DB_PASSWORD=/DB_PASSWORD=password/g' .env"
                            sh "sed -i -e 's/DB_CONNECTION=mysql/DB_CONNECTION=mysql/g' .env"
                            sh "sed -i -e 's/DB_HOST=127.0.0.1/DB_HOST=server-mysql/g' .env"
                            sh "sed -i -e 's/SIMPLE_USER_ROLE=/SIMPLE_USER_ROLE=simpleUser/g' .env"
                            sh "sed -i -e 's/ADMIN_ROLE=/ADMIN_ROLE=administrator/g' .env"
                            sh "sed -i -e 's/EMPLOYEE_ROLE=/EMPLOYEE_ROLE=employee/g' .env"

                            sh "sed -i -e 's/DB_DATABASE=thetiptop/DB_DATABASE=thetiptoptest/g' .env.testing"
                            sh "sed -i -e 's/DB_USERNAME=root/DB_USERNAME=thetiptop_user/g' .env.testing"
                            sh "sed -i -e 's/DB_PASSWORD=/DB_PASSWORD=password/g' .env.testing"
                            sh "sed -i -e 's/DB_CONNECTION=mysql/DB_CONNECTION=mysql/g' .env.testing"
                            sh "sed -i -e 's/DB_HOST=127.0.0.1/DB_HOST=server-mysql/g' .env.testing"
                            sh "sed -i -e 's/SIMPLE_USER_ROLE=/SIMPLE_USER_ROLE=simpleUser/g' .env.testing"
                            sh "sed -i -e 's/ADMIN_ROLE=/ADMIN_ROLE=administrator/g' .env.testing"
                            sh "sed -i -e 's/EMPLOYEE_ROLE=/EMPLOYEE_ROLE=employee/g' .env.testing"

                            sh 'cat  .env'
                            sh 'php artisan key:generate'
                            sh 'php artisan migrate'
                            sh 'php artisan jwt:secret'
                            sh 'php artisan db:seed'
                            sh 'php artisan generate:ticket'
                            sh 'vendor/bin/phpunit'
                            }

                         }

                        }

                        stage('Preprod - Deploy our image  in registry ') {
                                 docker.withRegistry( '', 'credential-docker' ) {
                                     dockerImage.push("latest")
                                 }
                          }

                          stage('Preprod Remote SSH') {
                            sshCommand remote: remote, command: 'ls -al'
                            sshCommand remote: remote, command: 'cd workflow && whoami && ./backupbd.sh develop && cd BackupAllDB && ls && cd .. && git branch && ./clear.sh develop backend'
                          }
                     }

                     if(env.BRANCH_NAME =="prod") {

                        stage('Building our image for prod ') {
                                 sh ' docker ps'
                                 sh 'docker -v'
                                 //dockerImage = docker.build "154265/laravel" + ":$BUILD_NUMBER"
                                 dockerImage = docker.build "154265/laravel" + ":latest"
                               }

                               stage('Prod Started container  and Run Unit and functionnal test ') {
                                          container =  docker.image('mysql:5.7').withRun('-e "MYSQL_DATABASE=${MYSQL_DATABASE}" -e "MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}" -e "MYSQL_PASSWORD=${MYSQL_ROOT_PASSWORD}" -e "MYSQL_USER=${MYSQL_USER}" --name server-mysql -p 3306:3306 ') { c ->
                                          docker.image('mysql:5.7').inside("--link ${c.id}:db") {
                                          sh 'while ! mysqladmin ping -hdb --silent; do sleep 1; done'
                                                 }

                                              containerLaravel =  dockerImage.inside("--link ${c.id}:db") {
                                                    sh 'composer install'
                                                    sh 'cp .env.example .env'
                                                    sh 'cat  .env'
                                                    sh 'php artisan key:generate'
                                                	sh "sed -i -e 's/DB_DATABASE=laravel/DB_DATABASE=thetiptop/g' .env"
                                                	sh "sed -i -e 's/DB_USERNAME=root/DB_USERNAME=thetiptop_user/g' .env"
                                                	sh "sed -i -e 's/DB_PASSWORD=root/DB_PASSWORD=password/g' .env"
                                                	sh "sed -i -e 's/DB_CONNECTION=mysql/DB_CONNECTION=mysql/g' .env"
                                                	sh "sed -i -e 's/DB_HOST=127.0.0.1/DB_HOST=server-mysql/g' .env"
                                                	sh "sed -i -e 's/SIMPLE_USER_ROLE=/SIMPLE_USER_ROLE=simpleUser/g' .env"
                                                    sh "sed -i -e 's/ADMIN_ROLE=/ADMIN_ROLE=administrator/g' .env"
                                                    sh "sed -i -e 's/EMPLOYEE_ROLE=/EMPLOYEE_ROLE=employee/g' .env"

                                                    sh "sed -i -e 's/DB_DATABASE=thetiptop/DB_DATABASE=thetiptop/g' .env.testing"
                                                    sh "sed -i -e 's/DB_USERNAME=root/DB_USERNAME=thetiptop_user/g' .env.testing"
                                                    sh "sed -i -e 's/DB_PASSWORD=/DB_PASSWORD=password/g' .env.testing"
                                                    sh "sed -i -e 's/DB_CONNECTION=mysql/DB_CONNECTION=mysql/g' .env.testing"
                                                    sh "sed -i -e 's/DB_HOST=127.0.0.1/DB_HOST=server-mysql/g' .env.testing"
                                                    sh "sed -i -e 's/SIMPLE_USER_ROLE=/SIMPLE_USER_ROLE=simpleUser/g' .env.testing"
                                                    sh "sed -i -e 's/ADMIN_ROLE=/ADMIN_ROLE=administrator/g' .env.testing"
                                                    sh "sed -i -e 's/EMPLOYEE_ROLE=/EMPLOYEE_ROLE=employee/g' .env.testing"

                                                	sh 'php artisan key:generate'
                                                    sh 'php artisan migrate'
                                                    sh 'php artisan jwt:secret'
                                                    sh 'php artisan db:seed'
                                                    sh 'php artisan generate:ticket'
                                                    sh 'php artisan config:cache --env=local'
                                                    sh ' php artisan config:clear'
                                                    sh 'vendor/bin/phpunit'
                                                    }

                                                 }

                                                }

                                                stage('Prod Deploy our image in registry') {
                                                         docker.withRegistry( '', 'credential-docker' ) {
                                                             dockerImage.push("latest")
                                                             dockerImage.push("${env.BUILD_NUMBER}")
                                                         }
                                                  }

                                                  stage('Prod Remote SSH') {
                                                    sshCommand remote: remote, command: 'ls -al'
                                                    sshCommand remote: remote, command: 'cd workflow && whoami && ./backupbd.sh prod && cd BackupAllDB && ls && cd .. && git branch && ./clear.sh prod backend'
                                                  }
                     }

                    /*
                 stage('Building our image') {
                       sh ' docker ps'
                       sh 'docker -v'

                    //dockerImage = docker.build "154265/laravel" + ":$BUILD_NUMBER"
                     dockerImage = docker.build "154265/laravel" + ":latest"


                         container =  docker.image('mysql:5.7').withRun('-e "MYSQL_DATABASE=${MYSQL_DATABASE_TEST}" -e "MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}" -e "MYSQL_PASSWORD=${MYSQL_ROOT_PASSWORD}" -e "MYSQL_USER=${MYSQL_USER}" --name server-mysql -p 3306:3306 ') { c ->
                            docker.image('mysql:5.7').inside("--link ${c.id}:db") {
                            sh 'while ! mysqladmin ping -hdb --silent; do sleep 1; done'
                         }

                      containerLaravel =  dockerImage.inside("--link ${c.id}:db") {
                        sh 'composer install'
                        sh 'cp .env.example .env'
                        sh 'php artisan key:generate'
                    	sh "sed -i -e 's/DB_DATABASE=laravel/DB_DATABASE=thetiptop/g' .env"
                    	sh "sed -i -e 's/DB_USERNAME=root/DB_USERNAME=thetiptop_user/g' .env"
                    	sh "sed -i -e 's/DB_PASSWORD=root/DB_PASSWORD=password/g' .env"
                    	sh "sed -i -e 's/DB_CONNECTION=mysql/DB_CONNECTION=mysql/g' .env"
                    	sh "sed -i -e 's/DB_HOST=127.0.0.1/DB_HOST=server-mysql/g' .env"
                    	sh "sed -i -e 's/SIMPLE_USER_ROLE=/SIMPLE_USER_ROLE=simpleUser/g' .env"
                        sh "sed -i -e 's/ADMIN_ROLE=/ADMIN_ROLE=administrator/g' .env"
                        sh "sed -i -e 's/EMPLOYEE_ROLE=/EMPLOYEE_ROLE=employee/g' .env"
                    	sh 'cat .env'
                    	sh 'php artisan key:generate'
                    	sh 'php artisan migrate'
                    	sh 'php artisan jwt:secret'
                    	sh 'php artisan db:seed'
                    	sh 'php artisan generate:ticket'
                    	sh 'vendor/bin/phpunit'
                        }
                        sh "docker ps"
                        sh "docker images"

                     }

                 }
                        //dockerImage.push("${env.BUILD_NUMBER}")
                          stage('Deploy our image') {
                                 docker.withRegistry( '', 'credential-docker' ) {
                                     dockerImage.push("latest")

                                 }
                          }

                          stage('Remote SSH') {
                                   sshCommand remote: remote, command: 'ls -al'
                                   sshCommand remote: remote, command: 'hostname'
                                   sshCommand remote: remote, command: 'cd workflow && whoami && git branch && $(docker-compose up -d) && ./clear.sh'
                                   // sshCommand remote: remote, command: 'cd workflow && whoami && git branch && data=$(git pull); if [[ "$data" == "Déjà à jour." ]] || [[ "$data" == "Already up to date." ]]; then echo "Successful"; else echo "Failed"; fi'
                                   // sshCommand remote: remote, command: 'cd workflow && whoami && git branch && data=$(docker ps -a -q  --filter ancestor=154265/laravel:latest); if [[ "$data" != "" ]]; then  $(docker stop "$data") &&$(docker-compose up -d)  ; else echo "$data"; fi'
                                   //sshCommand remote: remote, command: 'cd workflow && whoami && git branch && data=$(docker ps -a -q  --filter ancestor=154265/laravel:131); if [[ "$data" != "Déjà à jour." ]] || [[ "$data" != "Already up to date." ]]; then echo $(docker exec -T "$data" /bin/bash ) && echo "$data"; else echo "$data"; fi'
                                   // sshCommand remote: remote, command: 'data=$(docker images); if [[ "$data" != "" ]]; then echo "$data";  ; else echo "$data"; fi'
                                }


                                  stage('Cleaning up') {
                                   sh "docker rmi $registry:$BUILD_NUMBER"
                                  }
                      */
            }

     }
