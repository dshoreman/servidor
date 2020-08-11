#!/usr/bin/env bash

set -Eeo pipefail

SCRIPT_ROOT="$( cd "$(dirname "$0")" >/dev/null 2>&1; pwd -P )"

# shellcheck source=_io.sh
source "${SCRIPT_ROOT}/_io.sh"
# shellcheck source=_install.sh
source "${SCRIPT_ROOT}/_install.sh"
# shellcheck source=_templates.sh
source "${SCRIPT_ROOT}/_templates.sh"

trap 'echo && err "Aborted due to error" && exit 1' ERR
trap 'echo && err "Aborted by user" && exit 1' SIGINT

usage() {
    echo
    echo "Usage:"
    echo "  main.sh [-h | --help] [-v | --verbose]"
    echo "          [-b | --branch=<branch-name>]"
    echo
    echo "Options:"
    echo "  -b, --branch=BRANCH  Set the branch to install (defaults to master)"
    echo "  -h, --help           Display this help and exit"
    echo "  -v, --verbose        Print extra information during install"
    echo
}

main() {
    local servidor_branch

    echo && banner
    sanity_check
    parse_opts "$@"

    echo
    info "This script will prepare a fresh server and install Servidor."
    info "If this is not a fresh server, your mileage may vary."

    if is_interactive && ! ask "Continue with install?"; then
        err "Installation aborted." && exit 1
    fi

    start_install && install_servidor "${servidor_branch}"
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
    local -r OPTS=b:hv
    local -r LONG=branch:,help,verbose
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
            -b|--branch)
                servidor_branch="$2"; shift 2 ;;
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

    : "${servidor_branch:=master}"
    log "Set to install Servidor from branch ${servidor_branch}"
}

main "$@"
