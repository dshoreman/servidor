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

    "${GITHUB_WORKSPACE}/node_modules/.bin/eslint" \
        -c build/eslint/config.json "resources/js/**/*.{js,vue}" \
        && echo "Eslint ran without errors"
}

test_php() {
    local scriptDir="${GITHUB_WORKSPACE}/vendor/bin"

    if [[ "${CI_PHP_VERSION}" == "8.0" ]]; then
        run_php_cs "${scriptDir}"
    fi

    php "${scriptDir}/phpunit" -c build/phpunit/config.xml \
        --coverage-clover=coverage.xml --exclude-group "broken-travis"
}

run_php_cs() {
    "${1}/php-cs-fixer" fix --config=build/php-cs-fixer/config.php --verbose --diff --dry-run
    "${1}/phpcs" app --standard=PSR12
    "${1}/phpmd" app ansi build/phpmd/rules.xml
    "${1}/phpmnd" . --non-zero-exit-on-violation --exclude tests
    "${1}/phpstan" analyze -c build/phpstan/config.neon
    "${1}/psalm" -c build/psalm/psalm.xml
}

main
