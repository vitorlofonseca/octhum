Octhum
============

### Introduction

**Octhum** is a tool that allows an easy access of little enterprises to artificial intelligence, with transparency, and simplicity.

The solution is a REST API developed in PHP that works based in SaaS technology, allowing third-part software customisation.

**To more information, contact [vitorlofonseca@gmail.com](vitorlofonseca@gmail.com)**

### Install

1 - Clone project in apache folder

2 - Install composer

3 - Set apache document root to octhum/doc_root folder

4 - In octhum/frameworks/lumen/ folder, run the command "composer update --no-scripts" to install all lumen dependencies 

5 - Install MySql Server, and create a database

5 - Configure your environment variables (including database connection parameters) in octhum/frameworks/lumen/.env file (if .env doesn't exist, create it following the .env Laravel's model)

6 - Make some imports in php.ini, specifically mysqli.so, mysqlnd.so

7 - In octhum/frameworks/lumen/ folder, run the command "php artisan migrate" to run all lumen migrations

8 - Install PHP FANN

8.1 - Install FANN lib, running "dnf install fann-devel" (Fedora) or "sudo apt-get install libfann-dev" (Ubuntu)

8.2 - Install PECL module, running "dnf install php-pear". After that, install FANN-PHP, running "sudo pecl install fann"

8.3 - Import "fann.so" in php.ini file

If something be wrong, follow instructions in http://php.net/manual/pt_BR/fann.installation.php

9 - Serve API, running "php -S localhost:8000 -t public" in octhum/frameworks/lumen/

10 - Access http://localhost

