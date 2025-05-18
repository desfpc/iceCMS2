#!/bin/bash
set -e

INIT_MARKER="/var/www/html/.docker-init-done"

if [ ! -f "$INIT_MARKER" ]; then
  echo "🟡 Running first-time initialization..."

  apt-get update
  apt-get install -y libpng-dev git unzip libzip-dev zip curl
  docker-php-ext-install mysqli gd opcache zip
  pecl install redis
  docker-php-ext-enable redis

  # Установка Composer
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

  ##########################
  # 1. Инициализация ICECMS
  ##########################
  echo "🔧 Installing ICECMS..."
  cd /var/www/html
  composer install --no-interaction || true
  php cli.php migration-exec || true
  php cli.php make-symlinks || true

  ##########################
  # 2. Laravel
  ##########################
  if [ ! -f "/var/www/html/laravel/artisan" ]; then
    echo "🔧 Installing Laravel..."
    composer create-project laravel/laravel /var/www/html/laravel --no-interaction
  fi

  ##########################
  # 3. Symfony
  ##########################
  if [ ! -f "/var/www/html/symfony/composer.json" ]; then
    echo "🔧 Installing Symfony..."
    composer create-project symfony/skeleton /var/www/html/symfony --no-interaction
    cd /var/www/html/symfony
    composer require symfony/orm-pack symfony/maker-bundle --no-interaction
  fi

  ##########################
  # 4. Lumen
  ##########################
  if [ ! -f "/var/www/html/lumen/artisan" ]; then
    echo "🔧 Installing Lumen..."
    composer create-project laravel/lumen /var/www/html/lumen --no-interaction
  fi

  ##########################
  # 5. Fat-Free Framework (F3)
  ##########################
  if [ ! -d "/var/www/html/f3/vendor" ]; then
    echo "🔧 Installing Fat-Free Framework..."
    mkdir -p /var/www/html/f3
    cd /var/www/html/f3
    composer require bcosca/fatfree --no-interaction

    mkdir -p public
    cat <<EOF > public/index.php
<?php
require_once(__DIR__ . '/../vendor/autoload.php');
\$f3 = \Base::instance();
\$f3->route('GET /f3/test', function() {
    echo json_encode(["status" => "ok"]);
});
\$f3->run();
EOF
  fi

  # Файл-маркер
  touch "$INIT_MARKER"
  echo "✅ Initialization completed."
else
  echo "✅ Initialization already done, skipping..."
fi

exec php-fpm