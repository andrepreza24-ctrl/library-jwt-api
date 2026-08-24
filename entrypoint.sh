#!/bin/bash
set -e

if [ ! -f /var/www/artisan ]; then
  echo ""
  echo "================================================================"
  echo "  Primer arranque: creando el proyecto Laravel 12 desde cero"
  echo "================================================================"
  echo ""

  composer create-project laravel/laravel:^12.0 /tmp/laravel-tmp --no-interaction --prefer-dist

  cp -r /tmp/laravel-tmp/. /var/www/
  rm -rf /tmp/laravel-tmp

  cd /var/www

  echo ">> Instalando tymon/jwt-auth..."
  composer require tymon/jwt-auth:^2.2 --no-interaction

  echo ">> Copiando el código de la biblioteca sobre el esqueleto de Laravel..."
  mkdir -p /var/www/app/Http/Controllers/Api
  cp -r /overlay/app/Models/. /var/www/app/Models/
  cp -r /overlay/app/Http/Controllers/Api/. /var/www/app/Http/Controllers/Api/
  cp -r /overlay/app/Http/Requests/. /var/www/app/Http/Requests/
  cp -r /overlay/database/migrations/. /var/www/database/migrations/
  cp /overlay/routes/api.php /var/www/routes/api.php
  cp /overlay/config/auth.php /var/www/config/auth.php
  cp /overlay/bootstrap/app.php /var/www/bootstrap/app.php

  echo ">> Configurando .env..."
  if [ ! -f .env ]; then
    cp .env.example .env
  fi
  cat /overlay/env-extra.txt >> .env
  cat /overlay/env-extra.txt >> .env.example

  sed -i "s/^DB_HOST=.*/DB_HOST=db/" .env
  sed -i "s/^DB_DATABASE=.*/DB_DATABASE=library/" .env
  sed -i "s/^DB_USERNAME=.*/DB_USERNAME=laravel/" .env
  sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=laravel/" .env

  php artisan key:generate --force

  echo ">> Publicando configuración de JWT..."
  php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider" --no-interaction
  php artisan jwt:secret --force

  chmod -R 775 storage bootstrap/cache

  echo ">> Esperando a que MySQL esté listo..."
  for i in $(seq 1 30); do
    if php artisan migrate:status > /dev/null 2>&1; then
      break
    fi
    sleep 2
  done

  echo ">> Ejecutando migraciones..."
  php artisan migrate --force

  echo ""
  echo "================================================================"
  echo "  Proyecto listo."
  echo "================================================================"
  echo ""
else
  echo ">> El proyecto ya existe en ./src, arrancando servidor..."
fi

cd /var/www
exec php artisan serve --host=0.0.0.0 --port=8000
