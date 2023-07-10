# iceCMS2 v0.1a

Technology stack: PHP 8.2, MySQL 8 / MariaDB 11, Redis, Vue.js 3, Bootstrap 5

The main principles of iceCMS2 are simplicity and speed. Therefore, there are not many "standard" abstractions and
principles, such as ORM and 100% SOLID adherence. Direct SQL queries and fast native database drivers! No abstractions
for the sake of abstractions!

After the release of the stable 1st version, compatibility with other databases (such as PostgreSQL and MariaDB) is
planned. However, you can easily connect any database by writing a class that implements the interface
iceCMS2\DB\DBInterface

## Setup:
- Clone repository, set up your webserver to work with {repository foldder}/web
- cd to {repository foldder}
- modify ./settings/local.php for set up your server settings
- run: composer install (install composer first if needed)
- run: php cli.php migration-exec
- run: php cli.php make-symlinks
- view site in your browser (use webserver hosts settings url)

## Setup with docker:
- Clone repository
- cd to {repository foldder}
- run: docker-compose up
- run project php-fpm container's terminal:
- run in php-fpm container: cd /var/www/html
- run in php-fpm container: composer install
- run in php-fpm container: php cli.php migration-exec
- run in php-fpm container: php cli.php make-symlinks
- view site in your browser http://localhost:8181

## Work with iceCMS2 client:
- cd to {repository foldder}
- run: php cli.php help

## Create `admin` user:
- Create user (http://localhost:8181/registration if you use Docker)
- Manualy edit database table `users` and set `role` to `admin`

## Tests:
- create test database, name set in ./settings/local.php
- Run in ./test folder PHPUnit tests

## Full Wiki?
- English Wiki - comming soon
- Русская Wiki - тоже будет

# Support the project:
1. **Binance**  
Scan via the Binance App to send  
![Binance](https://github.com/desfpc/iceCMS2/assets/783571/342b15d9-b85d-4b08-b146-bc90c4074fb4 "Binance")  
My Pay ID: 444136543  
2. **USDT (TRC20)**  
TFK8xk5BE2YJjuf9mh9jVUchSCayZr9yJa  
3. **USDT (ERC20)**  
0x7dda48aad71e1319939b30eeda91efa9ea5582de  
