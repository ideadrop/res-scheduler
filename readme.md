Resource Scheduler
===================
**Resource Scheduler** is web application to allocate resources to projects in a virtual environment where the managers can easily know the engage status of each resources and can export necessary reports.

----------

Requirements
-------------
> - Make sure Redis is installed.
> - PHP >= 5.6.4
> - OpenSSL PHP Extension
> - PDO PHP Extension
> - Mbstring PHP Extension
> - Tokenizer PHP Extension
> - XML PHP Extension


Installation
-------------

#### Get Ready
>- Copy ".env.example" file to ".env"
>- Fill all environment fields in .env file
>- Make sure you have created DB and added DB credentials in .env file

#### Install
Go to project root and run
```
composer install
```
After composer finished installing its dependencies, run table migrations by running
```
php artisan migrate
```
When the migration is completed seed initial data by running
```
php artisan db:seed
```

Finally run app installer to setup application
```
php artisan app:install
```