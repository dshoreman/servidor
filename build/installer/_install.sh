install_servidor() {
    info "Installing Servidor..."
    git clone -q https://github.com/dshoreman/servidor.git /var/servidor

    log "Patching nginx config..."
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
