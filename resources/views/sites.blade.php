@extends ('layouts.app')

@section ('content')
<sui-card-group>
    <sui-card>
        <sui-card-content>
            <sui-card-header>Old Blog Archive</sui-card-header>
            <sui-card-meta>codeM0nK3Y.com</sui-card-meta>
        </sui-card-content>
        <sui-button attached="bottom">
            <sui-icon name="cogs"></sui-icon> Manage Site
        </sui-button>
    </sui-card>
    <sui-card>
        <sui-card-content>
            <sui-card-header>Personal Site</sui-card-header>
            <sui-card-meta>dsdev.io</sui-card-meta>
        </sui-card-content>
        <sui-button attached="bottom">
            <sui-icon name="cogs"></sui-icon> Manage Site
        </sui-button>
    </sui-card>
</sui-card-group>
@endsection
