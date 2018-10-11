<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'primary_domain',
        'is_enabled',
    ];
}
