#!/bin/bash
set -e

sudo supervisorctl stop all

git pull

# Install/update composer dependecies
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Run database migrations
php artisan migrate

sudo supervisorctl start all
