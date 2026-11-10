#!/bin/sh
set -eu
cd "$(dirname "$0")"
docker compose --profile testing up -d --wait app db-test
docker compose exec -T app php -r 'if (is_file("bootstrap/cache/config.php")) { fwrite(STDERR, "Clear cached configuration before preparing tests.\n"); exit(1); }'
docker compose exec -T app php artisan migrate --env=testing --force
docker compose exec -T app php artisan forum:verify-post-elements --env=testing
