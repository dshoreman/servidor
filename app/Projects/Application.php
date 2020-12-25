<?php

namespace Servidor\Projects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Servidor\Exceptions\System\UserNotFoundException;
use Servidor\System\User as SystemUser;

class Application extends Model
{
    private const SOURCE_PROVIDERS = [
        'bitbucket' => 'https://bitbucket.org/{repo}.git',
        'github' => 'https://github.com/{repo}.git',
    ];

    protected $appends = [
        'logs',
        'source_uri',
        'system_user',
    ];

    protected $fillable = [
        'template',
        'domain_name',
        'source_provider',
        'source_repository',
        'source_branch',
    ];

    protected $table = 'project_applications';

    public function getLogsAttribute(): array
    {
        return array_map(function ($log) {
            return $log->getTitle();
        }, $this->logs());
    }

    public function logs()
    {
        return $this->template()->getLogs();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getSourceUriAttribute(): string
    {
        $provider = $this->attributes['source_provider'];
        $repo = $this->attributes['source_repository'];

        if (array_key_exists($provider, self::SOURCE_PROVIDERS)) {
            return str_replace('{repo}', $repo, self::SOURCE_PROVIDERS[$provider]);
        }

        return $repo;
    }

    public function getSystemUserAttribute(): ?array
    {
        if (!$this->template()->requiresUser()) {
            return null;
        }

        try {
            $username = Str::slug($this->project->name);

            return SystemUser::findByName($username)->toArray();
        } catch (UserNotFoundException $e) {
            return null;
        }
    }

    public function template()
    {
        $template = 'Servidor\Projects\Applications\Templates\\' . $this->attributes['template'];

        return new $template($this);
    }
}
