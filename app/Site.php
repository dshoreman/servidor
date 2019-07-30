<?php

namespace Servidor;

use Illuminate\Database\Eloquent\Model;
use Servidor\Events\SiteUpdated;

class Site extends Model
{
    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    protected $dispatchesEvents = [
        'updated' => SiteUpdated::class,
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
