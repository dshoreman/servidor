@extends('layouts.login')

@section('content')
<div class="ui middle aligned center aligned grid">
    <div class="column">
        <h2 class="ui teal header centered">
            <i class="server icon"></i>
            Sign in to {{ config('app.name', '') }}
        </h2>
        <form class="ui form" method="POST" action="{{ route('login') }}">
            @csrf

            <div class="ui stacked segment">
                <div class="field">
                    <div class="ui left icon input" type="email" placeholder="Email address">
                        <input id="email" type="email" name="email"
                           placeholder="{{ __('E-mail address') }}"
                            class="{{ $errors->has('email') ? ' is-invalid' : '' }}"
                            value="{{ old('email') }}" required autofocus>
                        <i class="user icon"></i>

                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="field">
                    <div class="ui left icon input" type="password" placeholder="Password">
                        <input id="password" type="password" name="password" required
                            class="{{ $errors->has('password') ? ' is-invalid' : '' }}"
                            placeholder="{{ __('Password') }}" />
                        <i class="lock icon"></i>

                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="field left aligned">
                    <div class="ui toggle checkbox">
                        <input type="checkbox" name="remember"{{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">{{ __('Remember Me') }}</label>
                    </div>
                </div>
                <button class="ui teal fluid large button" type="submit">
                    {{ __('Login') }}
                </button>
            </div>
        </form>

        <div class="ui message">
            <a class="centered" href="{{ route('password.request') }}">
                {{ __('Forgot Your Password?') }}
            </a>
        </div>
    </div>
</div>
@endsection
