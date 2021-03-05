#!/bin/bash

cd /home/forge/api.wejunket.com
php artisan down
git pull origin master
composer install --no-interaction --prefer-dist --optimize-autoloader
sudo chmod 777 -R storage/logs/*
echo "" | sudo -S service php7.2-fpm reload

if [ -f artisan ]
then
    php artisan migrate --force
fi
php artisan up

