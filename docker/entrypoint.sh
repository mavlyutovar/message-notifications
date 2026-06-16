#!/bin/sh

# Устанавливаем зависимости, если их еще нет
composer install

# Генерируем документацию Swagger и оптимизируем Laravel
php artisan l5-swagger:generate
php artisan optimize

# Запускаем миграции (команда будет ждать, пока Postgres поднимется)
php artisan migrate --force

# Запускаем основной процесс контейнера 
exec php-fpm
