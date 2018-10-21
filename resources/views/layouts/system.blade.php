@extends('layouts.app')

@section ('content')
    <sui-grid>
        <sui-grid-column :width="4">
            <system-menu></system-menu>
        </sui-grid-column>

        <sui-grid-column stretched :width="12">
            <sui-segment>
                @yield('system-content')
            </sui-segment>
        </sui-grid-column>
    </sui-grid>
@stop
