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
