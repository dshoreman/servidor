has_passport_keys() {
    [ -f storage/oauth-private.key ] && [ -f storage/oauth-public.key ]
}

create_oauth_client() {
    if [ "$(oauth_clients)" = "0" ]; then
        log "Creating new oauth client..."
        sudo -Hu servidor php artisan passport:client -n --password \
            --name="Servidor API Client" | tail -n2 | cut -f3 -d' '
    else
        log "Fetching existing client..."

        local client_id
        client_id=$(oauth_client_id)
        echo -e "${client_id}\n$(oauth_secret "${client_id}")"
    fi
}

oauth_clients() {
    mysql -Ne "SELECT COUNT(*) FROM servidor.oauth_clients WHERE password_client=1"
}

oauth_client_id() {
    mysql -Ne "SELECT id FROM servidor.oauth_clients WHERE password_client=1 LIMIT 1"
}

oauth_secret() {
    mysql -Ne "SELECT secret FROM servidor.oauth_clients WHERE id=${1}"
}
