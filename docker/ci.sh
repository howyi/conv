#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

# tests/bootstrap.php opens a connection to every server in DB_HOST, so wait for
# all of them rather than only the last one compose starts.
IFS=',' read -ra hosts <<< "${DB_HOST:-127.0.0.1:3306}"
for host in "${hosts[@]}"; do
    ./docker/wait-for-it.sh -t 180 "$host"
done

vendor/bin/phpunit --coverage-clover=build/log/clover.xml
vendor/bin/phpstan analyse
vendor/bin/phpcs --standard=PSR12 src tests

if [ -n "${COVERALLS_REPO_TOKEN:-}" ]; then
    vendor/bin/php-coveralls -v
else
    echo 'COVERALLS_REPO_TOKEN is not set, skipping the coverage upload.'
fi
