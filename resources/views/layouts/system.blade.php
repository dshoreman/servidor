@extends('layouts.app')

@section ('content')
    <sui-grid>
        <sui-grid-column :width="4">
            <system-menu></system-menu>
        </sui-grid-column>

        <sui-grid-column :width="12">
            @yield('system-content')
        </sui-grid-column>
    </sui-grid>
@stop
