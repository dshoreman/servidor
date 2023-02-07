@if ($app->include_www && $app->config?->get('redirectWww'))
server {
@if ($app->config?->get('ssl'))
    listen 80;
    listen 443;

    ssl_certificate {{ $app->config?->get('sslCertificate') }};
    ssl_certificate_key {{ $app->config?->get('sslPrivateKey') }};

@endif
    server_name www.{{ $app->domain_name }};

    return 301 http{{ $app->config?->get('ssl') ? 's' : '' }}://{{ $app->domain_name }};
}

@endif
@if ($app->config?->get('ssl') && $app->config?->get('sslRedirect'))
server {
    listen 80;

@if ($app->include_www && !$app->config?->get('redirectWww'))
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

@if ($app->include_www && !$app->config?->get('redirectWww'))
    server_name {{ $app->domain_name }} www.{{ $app->domain_name }};
@else
    server_name {{ $app->domain_name }};
@endif

    root {{ $app->document_root }};
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?query_string;

        location ~ \.php$ {
            try_files $uri =404;

            fastcgi_pass unix:/var/run/php/php{{ $app->config?->get('phpVersion') ?? '8.0' }}-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

            include fastcgi_params;
        }
    }

    location ~ /\.ht {
        deny all;
    }
}
