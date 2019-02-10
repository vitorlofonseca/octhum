Octhum
============

### Introduction

There isn't an easy way to reuse artificial intelligence softwares. If you need to work with tumor patterns, you will need acquire a neural network that will have specific synapsis weights, and neuron quantities, applied to this scope. If you are a biologist and need recognize whales species by the their noises, you will need acquire another neural network platform, containing synapsis weights and neuron quantities completly different than the first case.

Octhum was made to solve this problem, training the intelligences with many models, verifying which model is the best, and using it. In this way, we can use various scopes in the same platform, with each scope using the the model that best suits. This is possible because Octhum require the same formatting in the database file content (showed below).

The solution is a REST API developed in PHP that works based in SaaS technology, allowing third-part software customisation (to forecast sells in a ERP, for example).

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

### Use

The first screen is the intelligences list, containing all created intelligences. To create an intelligence, you should to click in button "Create Intelligence"

![51731877_373631633368954_4241945447989510144_n](https://user-images.githubusercontent.com/16262664/52524390-8c700400-2c83-11e9-9769-a52fadec1194.png)

A windows will be opened, containing some informations to be filled. We will create an intelligence to decide the color, based in RGB inputted.

![51500057_2241259979419079_1891393182139154432_n](https://user-images.githubusercontent.com/16262664/52524426-03a59800-2c84-11e9-9514-366357d89fde.png)

Filled the informations, we should select a file (in the moment only CSV format. There are two file examples actually, in the "files/tests" folder) **in the pre-defined model**, below:

![screenshot_1](https://user-images.githubusercontent.com/16262664/52524456-54b58c00-2c84-11e9-8498-6bffcd13e1cc.png)

In our case, classifications are the three possible colors that we defined, can be green, blue or yellow. Variables are the inputs that we will give to algorithm, to decide the classification. Octhum doesn't limit the classifications neither variables quantity.

We will now input the informations to the Octhum process.

![51683576_331753787682897_7245524332733005824_n](https://user-images.githubusercontent.com/16262664/52524550-487dfe80-2c85-11e9-8a1d-dd8f8bb1e961.png)

Now that Octhum had learned about our scope, we will use the intelligence (clicking in "Use" button). A windows will appear, containing all variables defined in the database file (in our case, R, G and B). 

![51535845_2028857120542761_2086102487961436160_n](https://user-images.githubusercontent.com/16262664/52524580-9b57b600-2c85-11e9-8c31-476fe410db21.png)

Below, some obvious inputs made (255,0,0 obviously will be red, for example).

![52087610_343240326401306_7531001652027850752_n](https://user-images.githubusercontent.com/16262664/52524612-f2f62180-2c85-11e9-9d08-2189962e4caf.png)
![51713769_990908134436964_1715552307598327808_n](https://user-images.githubusercontent.com/16262664/52524614-f2f62180-2c85-11e9-898b-c61b6c23c9ef.png)
![51833993_613220589121533_7533631907179790336_n](https://user-images.githubusercontent.com/16262664/52524615-f2f62180-2c85-11e9-9848-4ab02f0fc915.png)

Below an example that isn't so obviously, matching with our interpretation, maybe.

![51813402_2365761056990229_6958327669310095360_n](https://user-images.githubusercontent.com/16262664/52524650-7b74c200-2c86-11e9-9011-6227dc5891f3.png)
![screenshot_2](https://user-images.githubusercontent.com/16262664/52524651-7b74c200-2c86-11e9-8af0-dec6c4d51dc4.png)

Below a GET request example, putting the concatenated inputs on URL to know the classification

![51747305_629296514150256_5706410501425070080_n](https://user-images.githubusercontent.com/16262664/52525035-be389900-2c8a-11e9-958c-3d7377aaf279.png)
