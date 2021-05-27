export DEBIAN_FRONTEND=noninteractive

start_install() {
    info "Adding required repositories..."
    add_repos && install_packages

    info "Enabling services..."
    enable_services mariadb nginx php8.0-fpm

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
    local phpexts=(php8.0-bcmath php8.0-curl php8.0-mbstring php8.0-xml php8.0-zip)

    info "Installing core packages..."
    install_pkg build-essential nodejs sysstat unzip zsh

    info "Installing database and web server..."
    install_pkg nginx php8.0-fpm

    info "Installing required PHP extensions..."

    is_vagrant && \
        log "Adding phpdbg and php-pcov for testing in Vagrant..." && \
        phpexts+=(php-pcov php8.0-phpdbg)

    install_pkg "${phpexts[@]}"

    info "Installing latest stable Composer..."
    install_composer

    info "Installing database..."
    install_pkg mariadb-server php8.0-mysql
}

install_composer() {
    local expected actual target=/tmp/composer-setup.php

    log " Fetching current installer checksum..."
    expected="$(curl -sSL https://composer.github.io/installer.sig)"
    log " Downloading installer..."
    curl -sSL https://getcomposer.org/installer > $target
    log " Comparing checksums..."
    actual="$(sha384sum $target | cut -d' ' -f1)"

    if [ "$actual" = "$expected" ]; then
        log " Checksums match! Starting install..."
        php $target --quiet --install-dir="/usr/local/bin" --filename="composer"
    fi
}

install_pkg() {
    log "Packages to install: ${*}"
    apt-get install -qy --no-install-recommends "${@}"
}

enable_services() {
    for svc in "${@}"; do
        systemctl enable "${svc}"
        systemctl restart "${svc}"
    done
}
