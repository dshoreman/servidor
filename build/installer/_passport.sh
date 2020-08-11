oauth_client_id() {
    mysql -Ne "SELECT id FROM servidor.oauth_clients WHERE password_client=1 LIMIT 1"
}

oauth_secret() {
    mysql -Ne "SELECT secret FROM servidor.oauth_clients WHERE id=${1}"
}
