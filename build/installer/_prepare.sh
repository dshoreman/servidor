export DEBIAN_FRONTEND=noninteractive

start_install() {
    info "Adding required repositories..."
    add_repos && install_packages

    info "Enabling services..."
    enable_services mariadb nginx php7.4-fpm

    if is_vagrant; then
        info "Adding vagrant user to www-data group..."
        usermod -aG www-data vagrant
    fi
}

add_repos() {
    log "Adding ondrej/nginx PPA"
    add-apt-repository -ny ppa:ondrej/nginx

    log "Adding ondrej/php PPA"
    add-apt-repository -ny ppa:ondrej/php

    log "Adding Nodesource repository"
    if is_vagrant; then
        # We don't need npm here, just update the repos
        log "Updating local repositories" && apt-get update
    else
        # This also runs apt-get update, so we don't have to.
        curl -sL https://deb.nodesource.com/setup_lts.x | bash -
    fi
}

install_packages() {
    local phpexts=(composer php7.4-bcmath php7.4-json php7.4-mbstring php7.4-xml php7.4-zip)

    info "Installing core packages..."
    install_required build-essential nodejs sysstat unzip zsh

    info "Installing database and web server..."
    install_required nginx php7.4-fpm

    info "Installing required PHP extensions..."

    is_vagrant && \
        log "Adding phpdbg and php-pcov for testing in Vagrant..." && \
        phpexts+=(php-pcov php7.4-phpdbg)

    install_required "${phpexts[@]}"

    info "Installing database..."
    install_required mariadb-server php7.4-mysql
}

install_required() {
    log "Packages to install: ${*}"
    apt-get install -qy --no-install-recommends "${@}"
}

enable_services() {
    for svc in "${@}"; do
        systemctl enable "${svc}"
        systemctl restart "${svc}"
    done
}
