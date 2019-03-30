@extends('layouts.login')

@section('content')
<div class="ui middle aligned center aligned grid">
    <div class="column">
        <h2 class="ui teal header centered">
            <i class="server icon"></i>
            {{ __('Verify Your Email Address') }}
        </h2>

        <form class="ui form" method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="ui stacked segment">
                @if (session('resent'))
                    <div class="alert alert-success" role="alert">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </div>
                @endif

                {{ __('Before proceeding, please check your email for a verification link.') }}
                {{ __('If you did not receive the email') }}, <a href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.

            </div>
        </form>
    </div>
</div>
@endsection
