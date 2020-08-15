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
    echo -e " \e[1;31m[ERROR]\e[21m ${*}\e[0m" >&2
}

# shellcheck disable=SC2009
is_interactive() {
    ps -o stat= -p $$ | grep -q '+'
}

is_vagrant() {
    [ "${SUDO_USER:=}" = "vagrant" ]
}

info() {
    echo -e " \e[1;36m[INFO]\e[0m ${*}\e[0m"
}

log() {
    if [[ ${debug:=} = true ]]; then
        echo -e " \e[1;33m[DEBUG]\e[0m ${*}" >&2
    fi
}

success() {
    echo -e " \e[1;32m[SUCCESS]\e[22m ${*}\e[0m"
}

edit_line() {
    local file="$1" match="$2" replace="$3" found val sVal sRep

    log "Setting ${match} to ${replace} in ${file}..."
    if ! grep -qP "^${match}=.*$" "${file}"; then
        echo "${match}=${replace}" >> "${file}"
        log " Done! No matches were found, so we added our own."
        return
    fi

    log " Existing values found, checking for exact match..."
    while IFS= read -r val; do
        if [ "$val" = "=$replace" ]; then
            found="true"
            log "  Perfect, found a match! Deleting any duplicates..." && \
                awk -v line="^${match}${val}$" -v prefix="^${match}=$" \
                    '($0 !~ line || ++n == 1) && $0 !~ prefix' "$file" \
                    > "/tmp/${file}.servitmp" && mv "/tmp/${file}.servitmp" "$file"
            break
        fi
    done < <(grep -oP "(?<=^${match})=.+" "${file}")

    while IFS= read -r val; do
        sVal="$(safe_srch "${val}")"
        sRep="$(safe_repl "${replace}")"

        if [ -n "$found" ] && [ "$val" = "=" ]; then
            log "  Deleting extraneous '${match}=' line..."
            sed -i "0,/^${match}=$/{//d}" "$file"
            found="true"; continue
        elif [ -n "$found" ]; then
            [ "$val" = "=${replace}" ] && continue
            log "  Commenting existing non-empty setting..."
            sed -i "0,/^\(${match}${sVal}\)$/{s//# \1/}" "$file"
            found="true"; continue
        elif [ "$val" = "=" ]; then
            log "  First match is empty! Editing line in-place..."
            sed -i "0,/^\(${match}=\)$/{s//\1${sRep}/}" "$file"
            found="true"; continue
        fi

        log "  Commenting first non-empty match, and adding our own..."
        sed -i "0,/^\(\(${match}\)${sVal}\)$/{s//# \1\n\2=${sRep}/}" "$file"
        found="true"
    done < <(grep -oP "(?<=^${match})=.*" "${file}")
}

safe_srch() {
    printf '%s\n' "${*}" | sed -e 's/[]\/$*.^[]/\\&/g'
}
safe_repl() {
    printf '%s\n' "${*}" | sed -e 's/[\/&]/\\&/g'
}
