#!/usr/bin/env bash
composer install --no-interaction

cp tests/migrations/create_user_settings_table.php vendor/laravel/laravel/database/migrations/2018_06_27_133337_create_user_settings_table.php

cd vendor/laravel/laravel
touch database/database.sqlite
echo "DB_CONNECTION=sqlite" > .env
composer update --no-interaction
php artisan migrate --no-interaction --force
cd -