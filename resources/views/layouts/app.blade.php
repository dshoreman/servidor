<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, height=device-height">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', '') }}</title>
    </head>
    <body>
        @yield('content')

        <script>
            window.stylePaths = {
                app: "{{ smart_asset('app') }}",
                theme: {
                    darkTweaks: "{{ smart_asset('theme.dark-custom') }}",
                    dark: "{{ smart_asset('theme.dark') }}",
                    light: "{{ smart_asset('theme.light') }}",
                },
            };
        </script>
        <script src="{{ smart_asset('app', 'js') }}"></script>
    </body>
</html>
