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

4 - In octhum/frameworks/lumen/ folder, run the command "composer update --no-scripts" to update all lumen dependencies 

5 - Configure your environment variables in octhum/frameworks/lumen/.env file (if .env doesn't exist, create it)

6 - In octhum/frameworks/lumen/ folder, run the command "php artisan migrate" to run all lumen migrations
