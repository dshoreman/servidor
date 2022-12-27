server {
@if ($app->include_www)
    server_name {{ $app->domain_name }} www.{{ $app->domain_name }};
@else
    server_name {{ $app->domain_name }};
@endif

    root {{ $app->document_root }};
    index index.html index.htm;

    location ~ /\.ht {
        deny all;
    }
}
