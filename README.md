## Shoogle

### Requirements:
- You must have installed docker and docker-compose
- At least docker version 20.10.2
- At least docker-compose version 1.27.4

### Installation:

#### 1. Running the Application with Docker Compose
We’ll now use docker-compose commands to build the application image and run the services we specified in our setup.
Build the app image with the following command:

`docker-compose build app`

#### 2. When the build is finished, you can run the environment in background mode with:

`docker-compose up -d`

This will run your containers in the background.

#### 3. To show information about the state of your active services, run:

`docker-compose ps`

#### 4. Environment Files
You must rename a `.env.example` file in the root of the project to just `.env`
Note: Make sure you have hidden files shown on your system.

#### 5. Composer
Laravel project dependencies are managed through the PHP Composer tool.
The first step is to install the depencencies by navigating into your project 
in terminal and typing this command:

`composer install`

#### 6.  NPM/Yarn
In order to install the Javascript packages for frontend development, you will need the Node Package Manager, and optionally the Yarn Package Manager by Facebook.
If you only have NPM installed you have to run this command from the root of the project:

`npm install`

If you have Yarn installed, run this instead from the root of the project:

`yarn`

#### 7. Create Database
```sh
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```
Change these lines to reflect your new database settings.
#### 8. Set Mail
```sh
MAIL_DRIVER=smtp
MAIL_HOST=maildev
MAIL_PORT=25
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```
Change these lines to reflect your new mail settings.

#### 9. Artisan Commands
The first thing we are going to so is set the key that Laravel will use when doing encryption.

`docker-compose exec app php artisan key:generate`

You should see a green message stating your key was successfully generated. As well as you should see the APP_KEY variable in your .env file reflected.

It's time to see if your database credentials are correct.

We are going to run the built in migrations to create the database tables:

`docker-compose exec app php artisan migrate`

You should see a message for each table migrated, if you don't and see errors, than your credentials are most likely not correct.

We are now going to set the administrator account information. To do this you need to navigate to this file and change the name/email/password of the Administrator account.

You can delete the other dummy users, but do not delete the administrator account or you will not be able to access the backend.

Now seed the database with:

`docker-compose exec app php artisan db:seed`

You should get a message for each file seeded, you should see the information in your database tables.

Also, you need to install passport with:

`docker-compose exec app  php artisan passport:install`

For handling the token encryption, generate a secret key by executing the following command

`docker-compose exec app php artisan jwt:secret`

We have successfully generated the JWT Secret key, and you can check this key inside the .env file.

`JWT_SECRET=secret_jwt_string_key`

#### 10. NPM Run '*'
Now that you have the database tables and default rows, you need to build the styles and scripts.

These files are generated using Laravel Mix, which is a wrapper around many tools, and works off the webpack.mix.js in the root of the project.

You can build with:

`npm run <command>`

where `<command>` is `dev` for example.

The available commands are listed at the top of the package.json file under the 'scripts' key.

You will see a lot of information flash on the screen and then be provided with a table at the end explaining what was compiled and where the files live.

At this point you are done, you should be able to hit the project in your local browser and see the project, as well as be able to log in with the administrator and view the backend.

#### 11. Login
The administrator credentials are:
```sh
Username: admin@admin.com
Password: secret
```
The user credentials are:
```sh
Username: user@user.com
Password: secret
```
The printer credentials are:
```sh
Username: printer@printer.com
Password: secret
```

### Examples of artisan commands in a docker environment:

Generate a database migration.

`docker-compose exec app php artisan make:migration create_new_table`

Running Migrations

`docker-compose exec app php artisan migrate`

Writing Seeders

`docker-compose exec app php artisan make:seeder NewSeeder`

Running Seeders

`docker-compose exec app php artisan db:seed --class=NewSeeder`

Generating Model Classes

`docker-compose exec app php artisan make:model SubDirectory/NewModel`

Resource Controllers

`docker-compose exec app php artisan make:controller SubDirectory/NewController --resource --model=Photo` 

Listing All Available Commands

`docker-compose exec app php artisan list`

Creating Form Requests

`docker-compose exec app php artisan make:request StoreNew`

Command composer

`docker-compose exec app composer install`

`docker-compose exec app composer update`

`docker-compose exec app composer dump-autoload`

`docker-compose exec app composer require pragmarx/version`

Clearing the log file

`docker-compose exec app echo "" > storage/logs/laravel.log`

Rolling Back Migrations

`docker-compose exec app php artisan migrate:rollback --step=1`

How to Check the MySQL Version

`docker-compose exec db mysql -V`

How to Check the mysqldump Version

`docker-compose exec db mysqldump -V`

Go to bash

`docker-compose exec app bash`

`ls -lFa`

`docker-compose db app bash`

`ls -lFa`

`docker-compose webserver app sh`

`ls -lFa`

Create dump

`docker exec db /usr/bin/mysqldump -u root --password=123qwe+++ obmenka > backup_average_rate.sql`

Expand dump

`cat /home/sasha/obmenka/backup_average_rate.sql | docker exec -i db /usr/bin/mysql -u root --password=123qwe+++ obmenka`

App version

`docker-compose exec app php artisan version:show`

`docker-compose exec app php artisan version:major`

`docker-compose exec app php artisan version:minor`

`docker-compose exec app php artisan version:patch`

`docker-compose exec app php artisan version:commit`

`docker-compose exec app php artisan version:absorb`

Clear config artisan

`docker-compose exec app php artisan config:clear`
`docker-compose exec app php artisan cache:clear`
`docker-compose exec app php artisan view:clear`
`docker-compose exec app php artisan route:clear`
`docker-compose exec app php artisan optimize:clear`





Clear log file

`docker-compose exec app echo "" > storage/logs/laravel-2021-06-09.log`

### Create a user with super-admin rights.

`docker-compose exec app php artisan shoogle:superadmin`

Answer the questions:

First Name?
Email?
Password ?
