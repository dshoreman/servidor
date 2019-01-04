<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, height=device-height">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Laravel</title>

        <link href="{{ asset('css/semantic.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div id="app">

            <sui-menu fixed inverted>
                <router-link header :to="{ name: 'dashboard' }" is="sui-menu-item">
                    <sui-icon name="server" size="big"></sui-icon> Servidor
                </router-link>
                @guest
                    <sui-menu-menu position="right">
                        <router-link :to="{ name: 'login' }" is="sui-menu-item">
                            {{ __('Login') }}
                        </router-link>
                        <router-link :to="{ name: 'register' }" is="sui-menu-item">
                            {{ __('Register') }}
                        </router-link>
                    </sui-menu-menu>
                @else
                    <div class="right menu">
                        <sui-dropdown item text="{{ Auth::user()->name }}">
                            <sui-dropdown-menu>
                                <a is="sui-dropdown-item" href="{{ route('logout') }}"
                                    @click.prevent="$refs.logoutForm.submit">
                                    {{ __('Logout') }}
                                </a>

                                <form ref="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </sui-dropdown-menu>
                        </sui-dropdown>
                    </div>
                @endguest
            </sui-menu>

            @yield('content')

        </div>

        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
