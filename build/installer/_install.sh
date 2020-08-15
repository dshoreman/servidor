install_servidor() {
    local app_url="http://servidor.local" branch="${1}"

    info "Installing Servidor..."
    clone_and_install

    info "Configuring application..."
    configure_application && install_passport

    log "Patching nginx config..."
    patch_nginx && systemctl reload nginx.service

    finalise && print_success
}

clone_and_install() {
    if ! is_vagrant; then
        git clone -qb "${branch}" https://github.com/dshoreman/servidor.git /var/servidor
    fi
    cd /var/servidor || (err "Could not clone Servidor!"; exit 1)

    log "Installing required Composer packages..."
    is_vagrant && cmd="sudo -Hu vagrant composer install" || cmd="composer install --no-dev"
    ${cmd} --no-interaction --no-progress --no-suggest

    log "Compiling static assets..."
    if is_vagrant; then
        info " Running in Vagrant, skipping asset build!"
        info " Run \`npm install && npm run dev\` to build for development."
    else
        npm_install && npm run prod
    fi
}

configure_application() {
    [ -f .env ] || cp .env.example .env

    log "Creating database..."
    create_database

    log "Generating secure app key..."
    if grep -qP "^APP_KEY=$" .env; then
        php artisan key:generate
    else
        log " SKIPPED! A key has already been generated."
    fi

    is_vagrant || app_url="http://$(hostname -f)"
    edit_line .env "APP_URL" "${app_url}"

    log "Migrating the database..."
    php artisan migrate --seed
}

create_database() {
    local password

    password="$(tr -dc 'a-zA-Z0-9!@#$%^&*()_+=,-.<>/?;:|[]{}~' < /dev/urandom | head -c28)"

    echo "DROP USER IF EXISTS 'servidor'@'localhost'; DROP DATABASE IF EXISTS servidor" | mysql && \
        echo "CREATE USER 'servidor'@'localhost' IDENTIFIED BY '${password}'" | mysql && \
        echo "GRANT ALL PRIVILEGES ON *.* TO 'servidor'@'localhost'; FLUSH PRIVILEGES;" | mysql && \
        echo "CREATE DATABASE servidor;" | mysql

    is_vagrant && echo "DROP DATABASE IF EXISTS servidor_testing; CREATE DATABASE servidor_testing;" | mysql

    edit_line .env "DB_PASSWORD" "\"${password}\""
}

install_passport() {
    local client

    has_passport_keys || php artisan passport:keys

    client="$(create_oauth_client)"
    edit_line .env "PASSPORT_CLIENT_ID" "$(head -n1 <<< "${client}")"
    edit_line .env "PASSPORT_CLIENT_SECRET" "$(tail -n1 <<< "${client}")"
}

patch_nginx() {
    nginx_config > /etc/nginx/sites-enabled/servidor.conf
    log " Writing default index page..."
    nginx_default_page > /var/www/html/index.nginx-debian.html

    # NOTE: This should be much more restrictive before final release!
    log " Setting permissions for www-data..."
    echo "www-data ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/www-data

    log " Taking ownership of the Servidor storage dir..."
    chown -R www-data:www-data /var/servidor/storage

    log "Setting owner to www-data on main web root..."
    chown www-data:www-data /var/www
}

finalise() {
    if ! ncurses_has_alacritty; then
        log " Missing ncurses >= 6.2, installing Alacritty terminfo..."
        wget -qP /tmp/ https://raw.githubusercontent.com/jwilm/alacritty/master/extra/alacritty.info
        tic -xe alacritty,alacritty-direct /tmp/alacritty.info && rm /tmp/alacritty.info
    fi

    log "Setting default shell for ${USER}..."
    chsh -s /bin/zsh

    if is_vagrant; then
        log "Setting default shell for vagrant..."
        chsh -s /bin/zsh vagrant

        log "Copying basic shell aliases for vagrant user..."
        vagrant_zshrc >> /home/vagrant/.zshrc && \
            chown vagrant:vagrant /home/vagrant/.zshrc
    fi
}

ncurses_has_alacritty() {
    infocmp alacritty > /dev/null 2>&1
}

npm_install() {
    if npm_has_ci; then
        npm ci
    else
        rm -rf node_modules && npm install
    fi
}

# The `ci` command was added in 5.7, but Ubuntu 18.04 ships 3.5.2.
# Using the Nodesource repo probably avoids that in most cases,
# but it's probably better to have the version check anyway.
npm_has_ci() {
    local ver

    ver="$(npm -v)"

    [ "${ver}" = "$(echo -e "${ver}\n5.7" | sort -V | tail -n1)" ]
}

print_success() {
    echo; success; success "Install Completed!"; success
    info
    info "An account has been created with the email"
    info "  'admin@servidor.local' and password 'servidor'."
    info
    info "Servidor is listening at the following addresses:"
    info "  ${app_url}:8042/"
    info "  http://$(dig +short myip.opendns.com @resolver1.opendns.com):8042/"
    info
    is_vagrant && info "Don't forget to npm ci && npm run dev!" && info
    echo
}
