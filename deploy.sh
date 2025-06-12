#!/bin/bash

echo "Installing dependencies"
docker compose exec -T php composer install --no-dev

echo "Migrating database"
docker compose exec -T php php artisan migrate --force

echo "Cleaning cache"
docker compose exec -T php php artisan config:clear
docker compose exec -T php php artisan cache:clear
docker compose exec -T php php artisan config:cache
docker compose exec -T php php artisan view:clear
docker compose exec -T php php artisan optimize:clear
docker compose exec -T php php artisan optimize

echo "Linking storage"
docker compose exec -T php php artisan storage:link