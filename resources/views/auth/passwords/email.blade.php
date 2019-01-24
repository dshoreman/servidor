@extends('layouts.login')

@section('content')
<div class="ui middle aligned center aligned grid">
    <div class="column">
        <h2 class="ui teal header centered">
            <i class="server icon"></i>
            {{ __('Reset Password') }}
        </h2>

        <form class="ui form" method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="ui stacked segment">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="field left aligned">
                    <label for="email">{{ __('E-Mail Address') }}</label>

                    <div class="ui left icon input">
                        <input id="email" type="email" name="email" required
                            class="{{ $errors->has('email') ? ' is-invalid' : '' }}"
                            value="{{ old('email') }}">
                        <i class="envelope icon"></i>

                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <button type="submit" class="ui teal fluid large button">
                    {{ __('Send Password Reset Link') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
