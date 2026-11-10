#!/bin/sh
set -eu
cd "$(dirname "$0")"
sh prepare-tests.sh
docker compose --profile testing --profile browser-testing up -d --wait test-web
docker compose exec -T -e APP_ENV=testing app php tests/Browser/composer-fixture.php
cleanup() {
    test_status=$?
    trap - EXIT
    docker compose exec -T -e APP_ENV=testing app php tests/Browser/composer-fixture.php cleanup || test_status=1
    docker compose --profile testing --profile browser-testing stop test-web || test_status=1
    exit "$test_status"
}
trap cleanup EXIT
docker compose --profile testing --profile browser-testing run --rm browser-tests
