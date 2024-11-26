## Require

Into ./lendora-librairie-api :
- Install composer (https://getcomposer.org/download/)
- Install php (https://www.php.net/manual/en/install.php)

Into ./lendora-librairie-front :
- Install npm
- Install angular/cli (npm install -g @angular/cli@17)

## Front
### Init the dependances
``
npm install
``
### Start the project
``
ng serve
``

## Back 
### Init the dependances
``
composer install
``
### Setup your database :

From the .env file, create a .env.local file and update the database URL accordingly.

- Setting up the database :
``
php bin/console doctrine:database:create
``
- Adapt the database schema : 
``
php bin/console doctrine:schema:update --force
``
- Load the fixtures :
``
php bin/console doctrine:fixtures:load
``
### Start the project
``
symfony serve
``
- For doc api : ``http://127.0.0.1:8000/api/doc``
- For API : ``http://127.0.0.1:8000/api/books``

