@if ($redirect->config?->get('ssl') && $redirect->config?->get('sslRedirect'))
server {
    listen 80;

@if ($redirect->include_www)
    server_name {{ $redirect->domain_name }} www.{{ $redirect->domain_name }};
@else
    server_name {{ $redirect->domain_name }};
@endif

    return 301 https://$host$request_uri;
}

@endif
server {
@if ($redirect->config?->get('ssl'))
@if (!$redirect->config?->get('sslRedirect'))
    listen 80;
@endif
    listen 443 ssl;

    ssl_certificate {{ $redirect->config?->get('sslCertificate') }};
    ssl_certificate_key {{ $redirect->config?->get('sslPrivateKey') }};
@endif

@if ($redirect->include_www)
    server_name {{ $redirect->domain_name }} www.{{ $redirect->domain_name }};
@else
    server_name {{ $redirect->domain_name }};
@endif

    return {{ $redirect->config->get('redirect')['type'] }} {{ $redirect->config->get('redirect')['target'] }};
}
