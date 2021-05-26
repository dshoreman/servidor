install_servidor() {
    local app_url="http://servidor.local" branch="${1}"

    info "Installing Servidor..."
    prepare_home && clone_and_install

    info "Configuring application..."
    configure_application

    log "Patching nginx config..."
    patch_nginx && systemctl reload nginx.service

    finalise && print_success
}

prepare_home() {
    if ! is_vagrant; then
        log "Creating servidor system user..."
        useradd -b /var -UG www-data -s /usr/sbin/nologin --system servidor
        mkdir /var/servidor && chown servidor:servidor /var/servidor
    else
        log "Skipped system user creation - when running in Vagrant, this is done by make dev-env."
    fi

    log "Adding www-data to the servidor group..."
    usermod -aG servidor www-data
}

clone_and_install() {
    cd /var/servidor || (err "Home directory for servidor was not created!"; exit 1)

    if ! is_vagrant; then
        sudo -u servidor git clone -qb "${branch}" https://github.com/dshoreman/servidor.git .
    fi

    log "Installing required Composer packages..."
    is_vagrant && c_dev="--prefer-source" || c_dev="--no-dev"
    sudo -Hu servidor composer install ${c_dev} --no-interaction --no-progress

    log "Compiling static assets..."
    if is_vagrant; then
        info " Running in Vagrant, skipping asset build!"
        info " Run \`npm install && npm run dev\` to build for development."
    else
        npm_install && sudo -Hu servidor npm run prod
    fi
}

configure_application() {
    [ -f .env ] || sudo -Hu servidor cp .env.example .env

    log "Creating database..."
    create_database

    log "Generating secure app key..."
    if grep -qP "^APP_KEY=$" .env; then
        sudo -Hu servidor php artisan key:generate
    else
        log " SKIPPED! A key has already been generated."
    fi

    is_vagrant || app_url="http://$(hostname -f)"
    edit_line .env "APP_URL" "${app_url}"

    log "Migrating the database..."
    sudo -Hu servidor php artisan migrate --seed
}

create_database() {
    local collation="CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci" password

    password="$(</dev/urandom tr -dc 'a-zA-Z0-9!@#$%^&*()_+=,-.<>/?;:|[]{}~' | head -c28)"

    echo "DROP USER IF EXISTS 'servidor'@'localhost'; DROP DATABASE IF EXISTS servidor" | mysql && \
        echo "CREATE USER 'servidor'@'localhost' IDENTIFIED BY '${password}'" | mysql && \
        echo "GRANT ALL PRIVILEGES ON *.* TO 'servidor'@'localhost'; FLUSH PRIVILEGES;" | mysql && \
        echo "CREATE DATABASE servidor ${collation};" | mysql

    is_vagrant && echo "DROP DATABASE IF EXISTS servidor_testing; CREATE DATABASE servidor_testing ${collation};" | mysql

    edit_line .env "DB_PASSWORD" "\"${password}\""
}

patch_nginx() {
    nginx_config > /etc/nginx/sites-enabled/servidor.conf
    log " Writing default index page..."
    nginx_default_page > /var/www/html/index.nginx-debian.html

    # NOTE: This should be much more restrictive before final release!
    log " Setting permissions for servidor..."
    echo "servidor ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/servidor && \
        chmod 0440 /etc/sudoers.d/servidor

    log " Setting permissions for www-data..."
    echo "www-data ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/www-data && \
        chmod 0440 /etc/sudoers.d/www-data

    log " Taking ownership of the Servidor storage dir..."
    chown -R servidor:www-data /var/servidor/storage

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

        log "Setting ownership on skeleton dir for tests..."
        chown -R www-data:www-data /var/servidor/resources/test-skel
    fi
}

ncurses_has_alacritty() {
    infocmp alacritty > /dev/null 2>&1
}

npm_install() {
    if npm_has_ci; then
        sudo -Hu servidor npm ci
    else
        sudo -Hu servidor rm -rf node_modules && sudo -Hu servidor npm install
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
    info "  http://$(dig -4 +short myip.opendns.com @resolver1.opendns.com):8042/"
    info
    is_vagrant && info "Don't forget to npm ci && npm run dev!" && info
    echo
}
