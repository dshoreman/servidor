#!/usr/bin/env bash

# shellcheck source=retry.sh
source "${GITHUB_WORKSPACE}/.github/scripts/retry.sh"

main() {
    sudo systemctl start mysql.service
    ( sudo mysql -e 'CREATE DATABASE servidor_testing;' && \
        echo "MySQL Database created!" ) || ( echo "Database creation failed"; exit 1 )

    cp .github/scripts/dotenv ./.env
    git config --global pull.ff only
    ci_retry composer install --no-interaction --ignore-platform-req=php

    php artisan key:generate
    php artisan migrate && php artisan passport:install
}

main
