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

# shellcheck disable=SC2009
is_interactive() {
    ps -o stat= -p $$ | grep -q '+'
}

info() {
    echo -e " \e[1;36m[INFO]\e[0m ${*}\e[0m"
}

log() {
    if [[ ${debug:=} = true ]]; then
        echo -e " \e[1;33m[DEBUG]\e[0m ${*}"
    fi
}

edit_line() {
    local file="$1" match="$2" replace="$3"

    # Bail out if exact match is found
    grep -qP "^${match}=${replace}$" "${file}" && return

    if grep -qP "^${match}=$" "${file}"; then
        # Option is unset, edit line in-place
        sed -i "s/^\(${match}\)=$/\1=${replace}/" "$file"
    else
        # Option is already set, comment it and append new line
        sed -i "s/^\(\(${match}\)=.\+\)$/#\1\n\2=${replace}/" "$file"
    fi
}
