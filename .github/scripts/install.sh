#!/usr/bin/env bash

# shellcheck source=retry.sh
source "${GITHUB_WORKSPACE}/.github/scripts/retry.sh"

main() {
    pecl install pcov
    sudo systemctl start mysql.service
    mysql -e 'CREATE DATABASE servidor_testing;'

    cp .github/scripts/dotenv ./.env
    git config --global pull.ff only
    ci_retry composer install --no-interaction

    php artisan key:generate
    php artisan migrate && php artisan passport:install
}

main
