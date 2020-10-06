<?php

function smart_asset(string $path, string $ext = 'css'): string
{
    $func = 'local' === app()->environment() ? 'mix' : 'asset';

    return $func($ext . '/' . $path . '.' . $ext);
}
