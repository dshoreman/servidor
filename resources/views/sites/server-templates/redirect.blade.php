server {
    server_name {{ $site->primary_domain }};

    return {{ $site->redirect_type }} {{ $site->redirect_to }};
}
