server {
    server_name {{ $redirect->domain_name }};

    return {{ $redirect->type }} {{ $redirect->target }};
}
