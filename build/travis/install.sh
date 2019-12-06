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
    mysql -e 'CREATE DATABASE servidor_test;'

    travis_retry composer install --no-interaction

    cp .env.travis .env
    php artisan key:generate
    php artisan migrate && php artisan passport:install
}

main
