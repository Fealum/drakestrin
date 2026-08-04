#!/bin/sh
set -eu

if [ -f /run/secrets/ssh_key ]; then
    mkdir -p /root/.ssh
    chmod 700 /root/.ssh
    rm -f /root/.ssh/id_rsa
    cp /run/secrets/ssh_key /root/.ssh/id_rsa
    chmod 600 /root/.ssh/id_rsa
fi

exec docker-php-entrypoint "$@"
