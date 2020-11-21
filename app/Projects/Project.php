<?php

namespace Servidor\Projects;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'is_enabled',
    ];

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
