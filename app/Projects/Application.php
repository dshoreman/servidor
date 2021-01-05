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
        'document_root',
        'logs',
        'source_root',
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

    private $templatesNamespace = 'Servidor\Projects\Applications\Templates\\';

    public function getDocumentRootAttribute(): string
    {
        return $this->sourceRoot . $this->template()->publicDir;
    }

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

    public function getSourceRepoNameAttribute(): string
    {
        $repo = $this->attributes['source_repository'];

        return mb_substr($repo, mb_strpos($repo, '/') + 1);
    }

    public function getSourceRootAttribute(): string
    {
        $tpl = $this->template();

        if ($tpl->requiresUser()) {
            // TODO: If user doesn't exist, this should throw UserNotFoundException
            //  (or some similar error) and NOT just default to a root-based path!
            return ($this->systemUser['dir'] ?? '') . '/' . $this->sourceRepoName;
        }

        return '/var/www/' . Str::slug($this->project->name) . '/' . $this->sourceRepoName;
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
        $template = $this->templatesNamespace . Str::studly(Str::lower($this->attributes['template']));

        return new $template($this);
    }
}
