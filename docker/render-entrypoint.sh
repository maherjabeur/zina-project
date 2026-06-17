#!/usr/bin/env bash
set -e

export PORT="${PORT:-10000}"

sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
envsubst '${PORT}' < /etc/apache2/sites-available/000-default.conf > /tmp/000-default.conf
mv /tmp/000-default.conf /etc/apache2/sites-available/000-default.conf

mkdir -p var/cache var/log
chown -R www-data:www-data var

php bin/console cache:clear --env=prod --no-debug

if [ -n "${DATABASE_URL:-}" ]; then
  php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod
  php bin/console app:ensure-admin --env=prod --no-debug
fi

exec "$@"
