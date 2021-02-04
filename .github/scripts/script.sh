#!/usr/bin/env bash

# shellcheck source=common.sh
source "${GITHUB_WORKSPACE}/.github/scripts/common.sh"

main() {
    local scriptDir="${GITHUB_WORKSPACE}/vendor/bin"

    # TODO:
    # This can probably be moved to a separate lint-php.sh, where
    # it can also be further split into groups, with one per tool.
    if [[ "${CI_PHP_VERSION}" == "8.0" ]]; then
        run_php_cs "${scriptDir}"
    fi

    # TODO:
    # As above for test-php.sh. Both lint and test could then be run as
    # separate jobs, with this script running in each as its own step.
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
