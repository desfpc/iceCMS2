#!/bin/bash
set -e

# Path to the marker file that indicates initialization has been done
INIT_MARKER="/var/www/html/.docker-init-done"

# Check if initialization has already been done
if [ ! -f "$INIT_MARKER" ]; then
  echo "Running first-time initialization..."

  # Run initialization commands
  apt-get update
  apt-get install -y libpng-dev git unzip libzip-dev zip
  docker-php-ext-install mysqli gd opcache zip
  pecl install redis
  docker-php-ext-enable redis
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
  cd /var/www/html
  composer install --no-interaction
  php cli.php migration-exec
  php cli.php make-symlinks

  # Create marker file to indicate initialization is done
  touch "$INIT_MARKER"
  echo "Initialization completed."
else
  echo "Initialization already done, skipping..."
fi

# Always start php-fpm
exec php-fpm
