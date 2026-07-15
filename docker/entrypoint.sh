#!/bin/sh
set -e

echo "Waiting for Postgres at ${DB_HOST:-db}:${DB_PORT:-5432}..."
until php -r "
try {
    new PDO('pgsql:host=${DB_HOST:-db};port=${DB_PORT:-5432};dbname=${DB_DATABASE:-bri_school}', '${DB_USERNAME:-postgres}', '${DB_PASSWORD:-postgres}');
    exit(0);
} catch (\Throwable \$e) {
    exit(1);
}
"; do
    sleep 1
done
echo "Postgres is up."

echo "Running database seed (idempotent)..."
php /var/www/html/public/database/seed.php || echo "Seed script reported an error (continuing to start the server)."

exec "$@"
