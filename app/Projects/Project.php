<?php

namespace Servidor\Projects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    protected $table = 'projects';

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function redirects(): HasMany
    {
        return $this->hasMany(Redirect::class);
    }
}
