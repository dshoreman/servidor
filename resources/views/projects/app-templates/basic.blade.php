server {
    server_name {{ $app->domain_name }};

    root {{ $app->document_root }};
    index index.html index.htm;

    location ~ /\.ht {
        deny all;
    }
}
