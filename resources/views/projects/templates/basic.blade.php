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

    return 301 http{{ $service->config?->get('ssl') ? 's' : '' }}://{{ $service->domain_name }};
@else
    server_name {{ $service->domain_name }};

    return 301 http{{ $service->config?->get('ssl') ? 's' : '' }}://www.{{ $service->domain_name }};
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
    index index.html index.htm;

    location ~ /\.ht {
        deny all;
    }
}
