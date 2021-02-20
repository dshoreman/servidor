<?php

use Illuminate\Support\Facades\App;

function smart_asset(string $path, string $ext = 'css'): string
{
    $path = $ext . '/' . $path . '.' . $ext;

    if (App::environment('local')) {
        return (string) mix($path);
    }

    return asset($path);
}
