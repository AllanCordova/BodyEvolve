#!/bin/sh
set -eu

php artisan migrate --force

if [ ! -e public/storage ]; then
  php artisan storage:link
fi

exec php artisan serve --host=0.0.0.0 --port=8000
