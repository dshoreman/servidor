server {
@if ($redirect->include_www)
    server_name {{ $redirect->domain_name }} www.{{ $redirect->domain_name }};
@else
    server_name {{ $redirect->domain_name }};
@endif

    return {{ $redirect->type }} {{ $redirect->target }}$request_uri;
}
