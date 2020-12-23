<?php

namespace Servidor\Projects;

use Illuminate\Database\Eloquent\Model;
use Str;

class Application extends Model
{
    private const SOURCE_PROVIDERS = [
        'bitbucket' => 'https://bitbucket.org/{repo}.git',
        'github' => 'https://github.com/{repo}.git',
    ];

    protected $appends = [
        'logs',
        'source_uri',
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
        return $this->template()->getLogPaths();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function readLog(string $log): string
    {
        $path = $this->logs[$log]['path'];

        if (!Str::startsWith($path, '/')) {
            $path = $this->project_root . '/' . $path;
        }

        exec('sudo cat ' . escapeshellarg($path), $file);

        return implode("\n", $file);
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

    public function template()
    {
        $template = 'Servidor\Projects\Applications\Templates\\' . $this->attributes['template'];

        return new $template();
    }
}
