<?php

namespace Servidor;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'primary_domain',
        'type',
        'source_repo',
        'document_root',
        'redirect_type',
        'redirect_to',
        'is_enabled',
    ];
}
