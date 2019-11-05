@extends ('layouts.app')

@section ('content')
<router-view id="app" version="{{ SERVIDOR_VERSION }}"></router-view>
@endsection
