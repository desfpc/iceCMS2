# iceCMS2 v0.1a

Technology stack: PHP 8.2, MySQL 8, Redis, Vue.js 3, Bootstrap 5

The main principles of iceCMS2 are simplicity and speed. Therefore, there are not many "standard" abstractions and
principles, such as ORM and 100% SOLID adherence. Direct SQL queries and fast native database drivers! No abstractions
for the sake of abstractions!

After the release of the stable 1st version, compatibility with other databases (such as PostgreSQL and MariaDB) is
planned. However, you can easily connect any database by writing a class that implements the interface
iceCMS2\DB\DBInterface

## Setup:
- Clone repository, setup your webserver to work with {repository foldder}/web
- cd to {repository foldder}
- run: composer install (install composer first if needed)
- run: php cli.php migration-exec
- view site in your browser (bu url that setup in webserver hosts settings)

## Setup with docker:
- Clone repository
- cd to {repository foldder}
- run: docker-compose up
- run project php-fpm container's terminal:
- run in php-fpm container: cd /var/www/html
- run in php-fpm container: composer install
- run in php-fpm container: php cli.php migration-exec
- view site in your browser http://localhost:8181


- English Wiki - comming soon.
- Русская Wiki - появится, но не так скоро...
