<?php

namespace Servidor\Projects;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
