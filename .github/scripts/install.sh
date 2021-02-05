#!/usr/bin/env bash

# shellcheck source=retry.sh
source "${GITHUB_WORKSPACE}/.github/scripts/retry.sh"
# shellcheck source=common.sh
source "${GITHUB_WORKSPACE}/.github/scripts/common.sh"

main() {
    local svc="${GITHUB_WORKSPACE}/.github/scripts/nginx.service"
    local skeleton="${GITHUB_WORKSPACE}/resources/test-skel"

    # TODO:
    # Lost the link, but there's a retry action we can use for this.
    # Not sure if running before there's a db is an issue, we'll see.
    group_start "Install composer packages" && \
        ci_retry composer install --no-interaction --ignore-platform-req=php
    group_end

    group_start "Prepare test database" && \
        start_mysql "Starting mysql.service" && \
        create_database "Creating database"
    group_end

    group_start "Set app config" && \
        copy_dotenv "Copying .env file from template" && \
        mk_app_key "Generating app encryption key"
    group_end

    group_start "Set Git's pull method" && \
        git config --global pull.ff only
    group_end

    group_start "Run database migrations" && \
        migrate_db && install_auth
    group_end

    group_start "Prepare test filesystem" && \
        sudo ln -sv "${GITHUB_WORKSPACE}" /var/servidor && \
        set_test_skel_permissions
    group_end
}

start_mysql() {
    echo -n "${1}..."
    ( sudo systemctl start mysql.service && msg_ok ) || msg_err
}
create_database() {
    echo "${1}..."
    ( sudo mysql -proot -e 'CREATE DATABASE servidor_testing;' && msg_ok ) || msg_err
}

copy_dotenv() {
    echo "${1}" && cp -v .github/scripts/dotenv ./.env
}
mk_app_key() {
    echo "${1}" && php artisan key:generate
}

set_test_skel_permissions() {
    sudo chown -Rv www-data:www-data "${skeleton}"
    sudo chown -Rv root:root "${skeleton}/protected"

    sudo chmod -v 777 "${skeleton}"
    sudo chmod -v 775 "${skeleton}"/mixed/another-dir
    sudo chmod -v 664 "${skeleton}"/mixed/hello.md "${skeleton}"/hidden/.bar
    sudo chmod -v 600 "${skeleton}/protected/forbidden"
}

migrate_db() {
    php artisan migrate
}
install_auth() {
    php artisan passport:install
}

main
