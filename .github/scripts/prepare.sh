#!/usr/bin/env bash

main() {
    svc="${GITHUB_WORKSPACE}/.github/scripts/nginx.service"
    skeleton="${GITHUB_WORKSPACE}/resources/test-skel"

    sudo cp "${svc}" /etc/systemd/system/ && sudo systemctl daemon-reload
    sudo mkdir -p /var/www /etc/nginx/sites-enabled /etc/nginx/sites-available
    sudo chmod -R 777 /etc/nginx /var/www

    sudo chown -R www-data:www-data "${skeleton}"
    sudo chown -R root:root "${skeleton}/protected"

    sudo chmod 777 "${skeleton}"
    sudo chmod 775 "${skeleton}"/mixed/another-dir
    sudo chmod 664 "${skeleton}"/mixed/hello.md "${skeleton}"/hidden/.bar
    sudo chmod 600 "${skeleton}/protected/forbidden"
}

main
