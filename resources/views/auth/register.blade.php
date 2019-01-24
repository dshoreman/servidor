@extends('layouts.login')

@section('content')
<div class="ui middle aligned center aligned grid">
    <div class="column">
        <h2 class="ui teal header centered">
            <i class="server icon"></i>
            Create your {{ config('app.name', '') }} Account
        </h2>
        <form class="ui form" method="POST" action="{{ route('register') }}">
            @csrf

            <div class="ui stacked segment">
                <div class="field left aligned">
                    <label for="name">{{ __('Name') }}</label>

                    <div class="ui left icon input">
                        <input id="name" type="text" name="name" required autofocus
                            class="{{ $errors->has('name') ? ' is-invalid' : '' }}"
                            value="{{ old('name') }}">
                        <i class="user icon"></i>

                        @if ($errors->has('name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="field left aligned">
                    <label for="email">{{ __('E-Mail Address') }}</label>

                    <div class="ui left icon input">
                        <input id="email" type="email" name="email"
                            class="{{ $errors->has('email') ? ' is-invalid' : '' }}"
                             value="{{ old('email') }}" required>
                        <i class="envelope icon"></i>

                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="field left aligned">
                    <label for="password">{{ __('Password') }}</label>

                    <div class="ui left icon input">
                        <input id="password" type="password" name="password" required
                            class="{{ $errors->has('password') ? ' is-invalid' : '' }}">
                        <i class="lock icon"></i>

                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="field left aligned">
                    <label for="password-confirm">{{ __('Confirm Password') }}</label>

                    <div class="ui left icon input">
                        <input id="password-confirm" type="password" name="password_confirmation" required>
                        <i class="lock icon"></i>
                    </div>
                </div>

                <button type="submit" class="ui teal large fluid button">
                    {{ __('Register') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
