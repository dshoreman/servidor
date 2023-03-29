@if ($service->config?->get('redirectWww'))
server {
@if ($service->config?->get('ssl'))
    listen 80;
    listen 443;

    ssl_certificate {{ $service->config?->get('sslCertificate') }};
    ssl_certificate_key {{ $service->config?->get('sslPrivateKey') }};

@endif
@if (1 === $service->config?->get('redirectWww'))
    server_name www.{{ $service->domain_name }};

    return 301 http{{ $service->config?->get('ssl') ? 's' : '' }}://{{ $service->domain_name }}$request_uri;
@else
    server_name {{ $service->domain_name }};

    return 301 http{{ $service->config?->get('ssl') ? 's' : '' }}://www.{{ $service->domain_name }}$request_uri;
@endif
}

@endif
@if ($service->config?->get('ssl') && $service->config?->get('sslRedirect'))
server {
    listen 80;

@if ($service->include_www && !$service->config?->get('redirectWww'))
    server_name {{ $service->domain_name }} www.{{ $service->domain_name }};
@else
    server_name {{ $service->domain_name }};
@endif

    return 301 https://$host$request_uri;
}

@endif
server {
@if ($service->config?->get('ssl'))
@if (!$service->config?->get('sslRedirect'))
    listen 80;
@endif
    listen 443 ssl;

    ssl_certificate {{ $service->config?->get('sslCertificate') }};
    ssl_certificate_key {{ $service->config?->get('sslPrivateKey') }};

@endif
@if ($service->include_www && !$service->config?->get('redirectWww'))
    server_name {{ $service->domain_name }} www.{{ $service->domain_name }};
@elseif ($service->include_www && 0 > $service->config?->get('redirectWww'))
    server_name www.{{ $service->domain_name }};
@else
    server_name {{ $service->domain_name }};
@endif

    root {{ $service->document_root }};
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?query_string;

        location ~ \.php$ {
            try_files $uri =404;

            fastcgi_pass unix:/var/run/php/php{{ $service->config?->get('phpVersion') ?? '8.1' }}-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

            include fastcgi_params;
        }
    }

    location ~ /\.ht {
        deny all;
    }
}
