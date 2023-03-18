export DEBIAN_FRONTEND=noninteractive

start_install() {
    info "Adding required repositories..."
    add_repos && install_packages

    info "Enabling services..."
    enable_services mariadb nginx php8.1-fpm

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
    local phpexts=(bcmath curl fpm mbstring mysql xml zip)

    info "Installing core packages..."
    install_pkg build-essential nodejs sysstat unzip zsh

    info "Installing database and web server..."
    install_pkg nginx mariadb-server

    info "Installing PHP and required extensions..."
    install_php_extensions "${phpexts[@]}"

    info "Installing latest stable Composer..."
    install_composer
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
        COMPOSER_HOME=/tmp/composer php $target --quiet \
            --install-dir="/usr/local/bin" --filename="composer"
    fi
}

install_php_extensions() {
    extensions=()

    for ext in "$@"; do
        extensions+=(php7.{0,1,2,3,4}-"${ext}")
        extensions+=(php8.{0,1,2}-"${ext}")
    done

    is_vagrant && \
        log "Adding phpdbg and php-pcov for testing in Vagrant..." && \
        extensions+=(php8.{0,1,2}-pcov)
        extensions+=(php8.{0,1,2}-phpdbg)

    extensions+=("php7.0-mcrypt" "php7.1-mcrypt")

    install_pkg "${extensions[@]}"
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
