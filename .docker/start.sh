#!/bin/sh
set -e

cd /var/www/html

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

# Creates the schema (if missing) and seeds sample data. Safe to re-run on
# every deploy/restart: every insert is guarded by an existence check.
php /var/www/html/public/database/seed.php || echo "Seed script reported an error (continuing to start the server)."

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
