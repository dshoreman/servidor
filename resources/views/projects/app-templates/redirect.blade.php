server {
@if ($redirect->config?->get('ssl'))
    listen 443 ssl;

    ssl_certificate {{ $redirect->config?->get('sslCertificate') }};
    ssl_certificate_key {{ $redirect->config?->get('sslPrivateKey') }};
@endif

@if ($redirect->include_www)
    server_name {{ $redirect->domain_name }} www.{{ $redirect->domain_name }};
@else
    server_name {{ $redirect->domain_name }};
@endif

    return {{ $redirect->type }} {{ $redirect->target }};
}
