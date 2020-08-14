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
    mysql -Ne "SELECT secret FROM servidor.oauth_clients WHERE id=${1}"
}
