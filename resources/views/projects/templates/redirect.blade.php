@if ($service->config?->get('ssl') && $service->config?->get('sslRedirect'))
server {
    listen 80;

@if ($service->include_www)
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

@if ($service->include_www)
    server_name {{ $service->domain_name }} www.{{ $service->domain_name }};
@else
    server_name {{ $service->domain_name }};
@endif

    return {{ $service->config->get('redirect')['type'] }} {{ $service->config->get('redirect')['target'] }};
}
