export DEBIAN_FRONTEND=noninteractive

start_install() {
    info "Adding required repositories..."
    add_repos && install_packages

    info "Enabling services..."
    enable_services mariadb nginx php7.4-fpm
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

enable_services() {
    for svc in "${@}"; do
        systemctl enable "${svc}"
        systemctl restart "${svc}"
    done
}
