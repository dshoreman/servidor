<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, height=device-height">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', '') }}</title>

        <link href="{{ asset('css/core.css') }}" rel="stylesheet" type="text/css">
    </head>
    <body>
        @yield('content')

        @if (app()->environment() == 'local')
            <script src="{{ mix('js/app.js') }}"></script>
        @else
            <script src="{{ asset('js/app.js') }}"></script>
        @endif
    </body>
</html>
