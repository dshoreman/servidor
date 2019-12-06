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

    "${scriptDir}/phpunit"
}

run_php_cs() {
    "${1}/php-cs-fixer" fix --config=.php_cs.dist --verbose --diff --dry-run --using-cache=no
    "${1}/phpcs" app --standard=PSR12
    "${1}/phpmd" app ansi .phpmd.xml
    "${1}/phpmnd" . --non-zero-exit-on-violation --exclude tests
    "${1}/phpstan" analyze -c phpstan.neon.dist
}

main
