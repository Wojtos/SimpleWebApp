#!/usr/bin/env bash

composer install -n
bin/console doctrine:migration:migrate --no-interaction
bin/console doctrine:fixtures:load --no-interaction
mkdir -p public/uploads
chmod -R 777 public/uploads

exec "$@"