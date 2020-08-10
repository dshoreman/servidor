#!/usr/bin/env bash

# shellcheck source=retry.sh
source "${TRAVIS_BUILD_DIR}/build/travis/retry.sh"

main() {
    if [[ "${RUN_MODE}" == "npm" ]]; then
        npm_install
    else
        php_install
    fi
}

npm_install() {
    travis_retry npm clean-install
}

php_install() {
    pecl install pcov
    mysql -e 'CREATE DATABASE servidor_test;'

    git config --global pull.ff only
    travis_retry composer install --no-interaction

    cp build/travis/dotenv ./.env
    php artisan key:generate
    php artisan migrate && php artisan passport:install
}

main
