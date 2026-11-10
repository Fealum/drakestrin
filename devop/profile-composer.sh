#!/bin/sh
set -eu
cd "$(dirname "$0")"
sh prepare-tests.sh
docker compose exec -T -e APP_ENV=testing app php tests/Browser/composer-fixture.php
trap 'docker compose exec -T -e APP_ENV=testing app php tests/Browser/composer-fixture.php cleanup' EXIT
docker compose exec -T -e APP_ENV=testing app php tests/Browser/composer-profile.php
