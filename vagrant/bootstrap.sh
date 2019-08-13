#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

main() {
    install_software
    configure_nginx

    set_group
    install_composer

    configure_shell
    configure_app
}

install_software() {
    update="apt-get update"
    install="apt-get install -qy --no-install-recommends"

    add-apt-repository ppa:ondrej/php

    $update && $install mariadb-server nginx openssl unzip zsh \
        php7.3-fpm php7.3-bcmath php7.3-json php7.3-mbstring php7.3-mysql php7.3-xml php7.3-zip

    start_service mariadb
}

configure_nginx() {
    cp -v /var/servidor/vagrant/nginx/servidor.conf /etc/nginx/sites-enabled/
    cp -v /var/servidor/vagrant/nginx/index.default.html /var/www/html/index.html
    rm -v /var/www/html/index.nginx-debian.html

    start_service php7.3-fpm
    start_service nginx
}

set_group() {
    usermod -aG www-data vagrant
}

install_composer() {
    curl -sS https://getcomposer.org/installer | \
       php -- --install-dir=/usr/local/bin --filename=composer
}

configure_shell() {
    wget -qP /tmp/ https://raw.githubusercontent.com/jwilm/alacritty/master/extra/alacritty.info
    tic -xe alacritty,alacritty-direct /tmp/alacritty.info && rm /tmp/alacritty.info

    chsh -s /bin/zsh && chsh -s /bin/zsh vagrant

    cp -v /var/servidor/vagrant/zsh.rc /home/vagrant/.zshrc && \
        chown vagrant:vagrant /home/vagrant/.zshrc
}

configure_app() {
    local client_id

    cd /var/servidor && echo "Configuring application..."

    chown www-data:www-data /var/www

    echo "CREATE USER 'servidor'@'localhost' IDENTIFIED BY 'vagrant'" | mysql && \
        echo "GRANT ALL PRIVILEGES ON *.* TO 'servidor'@'localhost'" | mysql && \
        echo "FLUSH PRIVILEGES; CREATE DATABASE servidor" | mysql && \
        echo "Database and user 'servidor' created."

    # Make sure both user and group are www-data for our tests
    chown -R www-data:www-data /var/servidor/resources/test-skel

    [ -d vendor ] || sudo -Hu vagrant composer -n install --no-progress --no-suggest
    [ -f .env ] || cp -v .env.example .env

    if grep -qP "^APP_KEY=$" .env; then
        php artisan key:generate
    fi

    php artisan migrate --seed

    has_passport_keys || php artisan passport:install

    if [ "$(oauth_clients)" = "0" ]; then
        php artisan passport:client -n --password --name="Servidor Password Grant Client"
    fi

    client_id=$(oauth_client_id)
    edit_line .env "PASSPORT_CLIENT_ID" "${client_id}"
    edit_line .env "PASSPORT_CLIENT_SECRET" "$(oauth_secret)"
}

edit_line() {
    local file="$1" match="$2" replace="$3"

    # Bail out if exact match is found
    grep -qP "^${match}=${replace}$" .env && return

    if grep -qP "^${match}=$" .env; then
        # Option is unset, edit line in-place
        sed -i "s/^\(${match}\)=$/\1=${replace}/" "$file"
    else
        # Option is already set, comment it and append new line
        sed -i "s/^\(\(${match}\)=.\+\)$/#\1\n\2=${replace}/" "$file"
    fi
}

has_passport_keys() {
    [ -f storage/oauth-private.key ] && [ -f storage/oauth-public.key ]
}

oauth_clients() {
    mysql -Ne "SELECT COUNT(*) FROM servidor.oauth_clients WHERE password_client=1"
}

oauth_client_id() {
    mysql -Ne "SELECT id FROM servidor.oauth_clients WHERE password_client=1 LIMIT 1"
}

oauth_secret() {
    mysql -Ne "SELECT secret FROM servidor.oauth_clients WHERE id=${client_id}"
}

start_service() {
    local service="$1"

    systemctl enable "$service"
    systemctl restart "$service"
}

main
