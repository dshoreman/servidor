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
