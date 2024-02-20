## Service consumption and invoice management API

### About this project

With this app you can manage customers, service consumption and invoices.

Through migrations you will obtain some fake entities to generate your first invoice.

The relationships between each entity are defined by the following class diagram:

<p align="center">
<img src="./resources/diagram/img.png" width="700" alt="Class diagram">
</p>



### About Development 
The application is written on Laravel + Sail to have a dockerized development environment in a very short time. 
DB engine is Mysql in two separate instances (application and testing)

Some SOLID principles were applied, for example in **[this commit](https://github.com/colomuller91/tenet-api-test/commit/bd44cf67e6e27f1f1bc312e2fb88d88431ecb5ae)**, Single Responsibility Principle

Factories and seeders were used to create initial data.

Tests were developed based on api routes to check main flow.

### Invoice totals calculation

For each detail of invoice, the strategy to find subtotal was simple as: `quantity * unit_price`.
The thing is when you have to deal with more variables, like Storage service, where you have to
bill service usage depends on `storage amount`, `storage time` and `unit_price`. 

So the resolution was store in consumption table, 
the quantity of GB in `quantity` and period in `from_date` and `to_date`, so when you 
want to bill this consumption, quantity in invoice detail represents `GBs * days`.

You can find unit_price in both `services` and `invoice_details` tables to save service price from the exact moment 
when you create the invoice.


### How to install

You only need a few dependencies:  php, composer, docker and docker-compose to start the application:

```
### first clone the repo
git clone https://github.com/colomuller91/tenet-api-test.git    

### move to the project folder and install dependencies
cd tenet-api-test
composer install

### copy env
cp .env.example .env

### install sail, only need to include mysql service for this application
php artisan sail:install

### (optional) add sail command as an alias, in this case is for zsh bash
echo 'alias sail="./vendor/bin/sail"' >> ~/.zshrc

### start the application
sail up -d

### generate key
sail artisan key:gen

### insert initial data
sail artisan migrate --seed

### if you want to start over with another random data
sail artisan migrate:fresh --seed

### run tests (test are running in testing instance db, so your main db instance is safe :)
sail artisan test

```


### Application endpoins

You can list all app endpoints using `artisan` command

```
sail artisan route:list --path=api
```

Also, you can find a <b>postman collection</b> in root folder


<hr>
<h3 align="center">
    <b>Juan Pablo Müller</b><br>
</h3>
<h4 align="center">
    <a href="https://www.linkedin.com/in/colomu/">LinkedIn</a><br>
    <a href="https://colomuller91.github.io">Online resume</a><br>
    <a href="https://github.com/colomuller91">Personal repo</a><br>
</h4>
