#!/usr/bin/env bash
set -Eeo pipefail
trap 'echo && err "Aborted due to error" && exit 1' ERR
trap 'echo && err "Aborted by user" && exit 1' SIGINT
usage() {
    echo
    echo "Usage:"
    echo "  bash ./setup.sh [-h | --help] [-v | --verbose]"
    echo
    echo "Options:"
    echo "  -h, --help        Display this help and exit"
    echo "  -v, --verbose     Print extra information during install"
    echo
}
main() {
    echo && banner
    sanity_check
    parse_opts "$@"
    echo
    info "This script will prepare a fresh server and install Servidor."
    info "If this is not a fresh server, your mileage may vary."
    if ! ask "Continue with install?"; then
        err "Installation aborted." && exit 1
    fi
    start_install
    install_servidor
}
sanity_check() {
    # shellcheck disable=SC2251
    ! getopt -T > /dev/null
    if [[ ${PIPESTATUS[0]} -ne 4 ]]; then
        echo "Enhanced getopt is not available. Aborting."
        exit 1
    fi
    if [ "${BASH_VERSINFO:-0}" -lt 4 ]; then
        echo "Your version of Bash is ${BASH_VERSION} but install requires at least v4."
        exit 1
    fi
}
parse_opts() {
    local -r OPTS=hv
    local -r LONG=help,verbose
    local parsed
    # shellcheck disable=SC2251
    ! parsed=$(getopt -o "$OPTS" -l "$LONG" -n "$0" -- "$@")
    if [[ ${PIPESTATUS[0]} -ne 0 ]]; then
        echo "Run 'install.sh --help' for a list of commands."
        exit 2
    fi
    eval set -- "$parsed"
    while true; do
        case "$1" in
            -h|--help)
                usage && exit 0 ;;
            -v|--verbose)
                debug=true; shift ;;
            --)
                shift; break ;;
            *)
                echo "Option '$1' should be valid but couldn't be handled."
                echo "Please submit an issue at https://github.com/dshoreman/servidor/issues"
                exit 3 ;;
        esac
    done
    log "Debug mode enabled"
}
install_servidor() {
    info "Installing Servidor..."
    git clone -q https://github.com/dshoreman/servidor.git /var/servidor
}
ask() {
    echo
    echo -e " \e[1;34m${1}\e[21m [yN]\e[0m"
    read -rp" " response
    [[ "${response,,}" =~ ^(y|yes)$ ]]
}
banner() {
    echo -e " \e[1;36m======================"
    echo "   Servidor Installer"
    echo -e " ======================\e[0m"
}
err() {
    echo -e " \e[1;31m[ERROR]\e[21m ${*}\e[0m"
}
info() {
    echo -e " \e[1;36m[INFO]\e[0m ${*}\e[0m"
}
log() {
    if [[ ${debug:=} = true ]]; then
        echo -e " \e[1;33m[DEBUG]\e[0m ${*}"
    fi
}
export DEBIAN_FRONTEND=noninteractive
start_install() {
    info "Adding required repositories..."
    add_repos && install_packages
}
add_repos() {
    log "Adding ondrej/nginx PPA"
    add-apt-repository -ny ppa:ondrej/nginx
    log "Adding ondrej/php PPA"
    add-apt-repository -ny ppa:ondrej/php
    log "Updating local repositories"
    apt-get update
}
install_packages() {
    info "Installing core packages..."
    install_required sysstat unzip zsh
    info "Installing database and web server..."
    install_required nginx php7.4-fpm
    info "Installing required PHP extensions..."
    install_required composer php7.4-bcmath php7.4-json php7.4-mbstring php7.4-xml php7.4-zip
    info "Installing database..."
    install_required mariadb-server php7.4-mysql
}
install_required() {
    for pkg in "${@}"; do
        log "Installing package ${pkg}..."
        apt-get install -qy --no-install-recommends "${pkg}"
    done
}
main "$@"
