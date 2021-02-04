#!/usr/bin/env bash

group_start() {
    echo "::group::${1}"
}
group_end() {
    echo "::endgroup::"
}

msg_ok() {
    echo -e "\e[1;32m [OK]\e[0m"
}
msg_err() {
    echo -e "\e[1;32m [ERR]\e[0m" && exit 1
}
