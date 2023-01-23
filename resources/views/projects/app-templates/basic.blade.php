@if ($app->config?->get('ssl') && $app->config?->get('sslRedirect'))
server {
    listen 80;

@if ($app->include_www)
    server_name {{ $app->domain_name }} www.{{ $app->domain_name }};
@else
    server_name {{ $app->domain_name }};
@endif

    return 301 https://$host$request_uri;
}

@endif
server {
@if ($app->config?->get('ssl'))
@if (!$app->config?->get('sslRedirect'))
    listen 80;
@endif
    listen 443 ssl;

    ssl_certificate {{ $app->config?->get('sslCertificate') }};
    ssl_certificate_key {{ $app->config?->get('sslPrivateKey') }};
@endif

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
