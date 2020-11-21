<?php

namespace Servidor\Projects;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'template',
        'domain_name',
        'source_repository',
        'source_branch',
    ];

    protected $table = 'project_applications';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
