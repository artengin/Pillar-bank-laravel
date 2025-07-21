#!/bin/bash
composer install

if [[ -f .env ]]; then
  echo ".env already exists"
else
  cp .env.example .env
  php artisan key:generate
  php artisan jwt:secret --force
fi

if [[ -f .env.testing ]]; then
  echo ".env.testing already exists"
else
  cp .env.testing.example .env.testing
  php artisan key:generate --env=testing
  php artisan jwt:secret --env=testing --force
fi

php artisan migrate --force
chmod -R 777 storage
