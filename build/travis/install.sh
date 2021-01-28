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
    mysql -e 'CREATE DATABASE servidor_testing;'

    cp build/travis/dotenv ./.env
    git config --global pull.ff only
    if [[ "${TRAVIS_PHP_VERSION:0:3}" == "7.3" ]]; then
        travis_retry composer install -n --ignore-platform-req=php
    else
        travis_retry composer install --no-interaction
    fi

    php artisan key:generate
    php artisan migrate && php artisan passport:install
}

main
