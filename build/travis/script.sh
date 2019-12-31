#!/usr/bin/env bash

main() {
    if [[ "${RUN_MODE}" == "npm" ]]; then
        test_npm
    else
        test_php
    fi
}

test_npm() {
    npm run prod
}

test_php() {
    local scriptDir="${TRAVIS_BUILD_DIR}/vendor/bin"

    if [[ "${RUN_MODE}" == "php-latest" ]]; then
        run_php_cs "${scriptDir}"
    fi

    phpdbg -qrr "${scriptDir}/phpunit" -c build/phpunit/config.xml
}

run_php_cs() {
    "${1}/php-cs-fixer" fix --config=build/php-cs-fixer/config.php --verbose --diff --dry-run
    "${1}/phpcs" app --standard=PSR12
    "${1}/phpmd" app ansi build/phpmd/rules.xml
    "${1}/phpmnd" . --non-zero-exit-on-violation --exclude tests
    "${1}/phpstan" analyze -c build/phpstan/config.neon
}

main
