#!/usr/bin/env bash

# shellcheck source=common.sh
source "${GITHUB_WORKSPACE}/.github/scripts/common.sh"

main() {
    group_start "Prepare test database" && \
        start_mysql "Starting mysql.service" && \
        create_database "Creating database"
    group_end

    group_start "Set app config" && \
        copy_dotenv "Copying .env file from template" && \
        mk_app_key "Generating app encryption key" && \
        git config --global pull.ff only
    group_end

    group_start "Run database migrations" && \
        migrate_db
    group_end

    group_start "Prepare test filesystem" && \
        sudo ln -sv "${GITHUB_WORKSPACE}" /var/servidor && \
        set_test_skel_permissions
    group_end

    group_start "Build frontend assets" && \
        npm ci && npm run dev
    group_end
}

start_mysql() {
    echo -n "${1}..."
    ( sudo systemctl start mysql.service && msg_ok ) || msg_err
}
create_database() {
    echo "${1}..."
    ( sudo mysql -proot -e 'CREATE DATABASE servidor_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;' && msg_ok ) || msg_err
}

copy_dotenv() {
    echo "${1}" && cp -v .github/scripts/dotenv ./.env
}
mk_app_key() {
    echo "${1}" && php artisan key:generate
}

set_test_skel_permissions() {
    local skeleton="${GITHUB_WORKSPACE}/resources/test-skel"

    sudo chown -Rv www-data:www-data "${skeleton}"
    sudo chown -Rv root:root "${skeleton}/protected"

    sudo chmod -v 777 "${skeleton}"
    sudo chmod -v 666 "${skeleton}"/clearable.txt
    sudo chmod -v 775 "${skeleton}"/mixed/another-dir
    sudo chmod -v 664 "${skeleton}"/mixed/hello.md "${skeleton}"/hidden/.bar
    sudo chmod -v 600 "${skeleton}/protected/forbidden"
}

migrate_db() {
    php artisan migrate
}

main
