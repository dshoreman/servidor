#!/usr/bin/env bash

main() {
    if [[ "${RUN_MODE}" == "npm" ]]; then
        return
    fi

    skeleton="${TRAVIS_BUILD_DIR}/resources/test-skel"

    sudo chmod 777 "${skeleton}"
    sudo chown -R www-data:www-data "${skeleton}"
    sudo chown -R root:root "${skeleton}/protected"
    sudo chmod 600 "${skeleton}/protected/forbidden"
}

main
