<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, height=device-height">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', '') }}</title>

        <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div id="app">
            @yield('content')
        </div>

        <footer>
            <p>Servidor v{{ SERVIDOR_VERSION }}</p>
        </footer>

        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
