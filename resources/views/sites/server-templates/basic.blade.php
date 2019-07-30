server {
    server_name {{ $site->primary_domain }};

    root {{ $site->document_root }};
    index index.html index.htm;

    location ~ /\.ht {
        deny all;
    }
}
