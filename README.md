**UTILISATION DU PROJET**

_Les commandes à taper_

`composer update`

`php artisan key:generate`

`php artisan jwt:secret`

**Dans le fichier _.env_**

_Copier coller cette ligne ett remplacer par les valeurs que vous voulez_

`ADMIN_ROLE=administrator`

`SIMPLE_USER_ROLE=simpleUser`

`EMPLOYEE_ROLE=employee`

`ADMIN_NAME=admin-name`

`ADMIN_EMAIL=account@yahoo.fr`

`ADMIN_TEL=0000000`

`ADMIN_ADRESSE=addresse`

`ADMIN_ROLE=administrator`

`ADMIN_PASSWORD=password`

_Au niveau du terminal taper les commande_

`php artisan migrate`

`php artisan db:seed`

_Au niveau de votre navigateur taper 

`http://localhost:8000/tickets/generer-les-tickets`

**Pour générer les tickets via l'invite de command** 

` php artisan generate:ticket`

`php artisan config:cache --env=local
`

` php artisan config:clear
`


