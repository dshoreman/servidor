#!/usr/bin/env bash

# shellcheck source=retry.sh
source "${CI_BUILD_DIR}/.github/scripts/retry.sh"

main() {
    if [[ "${RUN_MODE}" == "npm" ]]; then
        npm_install
    else
        php_install
    fi
}

npm_install() {
    ci_retry npm clean-install
}

php_install() {
    pecl install pcov
    mysql -e 'CREATE DATABASE servidor_testing;'

    cp .github/scripts/dotenv ./.env
    git config --global pull.ff only
    ci_retry composer install --no-interaction

    php artisan key:generate
    php artisan migrate && php artisan passport:install
}

main
