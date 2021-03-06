<?php

namespace Servidor\Projects;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Servidor\Exceptions\System\UserNotFoundException;
use Servidor\Projects\Applications\LogFile;
use Servidor\Projects\Applications\Templates\Html;
use Servidor\Projects\Applications\Templates\Laravel;
use Servidor\Projects\Applications\Templates\Php;
use Servidor\Projects\Applications\Templates\Template;
use Servidor\System\User as SystemUser;

/**
 * An Application is a Project component for websites, apps or server processes.
 *
 * @property int     $id
 * @property int     $project_id
 * @property string  $template
 * @property string  $domain_name
 * @property string  $source_provider
 * @property string  $source_repository
 * @property string  $source_branch
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property string  $document_root
 * @property array   $logs
 * @property string  $source_repo_name
 * @property string  $source_root
 * @property string  $source_uri
 * @property ?array  $system_user
 * @property Project $project
 *
 * @method static Builder|Application query()
 * @method static Builder|Application whereCreatedAt($value)
 * @method static Builder|Application whereDomainName($value)
 * @method static Builder|Application whereId($value)
 * @method static Builder|Application whereProjectId($value)
 * @method static Builder|Application whereSourceBranch($value)
 * @method static Builder|Application whereSourceProvider($value)
 * @method static Builder|Application whereSourceRepository($value)
 * @method static Builder|Application whereTemplate($value)
 * @method static Builder|Application whereUpdatedAt($value)
 */
class Application extends Model
{
    public const SOURCE_PROVIDERS = [
        'bitbucket' => 'https://bitbucket.org/{repo}.git',
        'github' => 'https://github.com/{repo}.git',
        'custom' => '{repo}',
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

    /** @var string */
    private $templatesNamespace = 'Servidor\Projects\Applications\Templates\\';

    public function getDocumentRootAttribute(): string
    {
        return $this->sourceRoot . $this->template()->publicDir();
    }

    public function getLogsAttribute(): array
    {
        return array_map(fn (LogFile $log): string => $log->getTitle(), $this->logs());
    }

    public function logs(): array
    {
        return $this->template()->getLogs();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getSourceRepoNameAttribute(): string
    {
        $repo = (string) $this->attributes['source_repository'];

        if (false === ($pos = mb_strpos($repo, '/'))) {
            return $repo;
        }

        return (string) mb_substr($repo, $pos + 1);
    }

    public function getSourceRootAttribute(): string
    {
        $tpl = $this->template();

        if ($tpl->requiresUser() && $this->systemUser) {
            // TODO: If user doesn't exist, this should throw UserNotFoundException
            //  (or some similar error) and NOT just default to a root-based path!
            return ((string) $this->systemUser['dir']) . '/' . $this->sourceRepoName;
        }

        /** @var \Servidor\Projects\Project */
        $project = $this->project;

        return '/var/www/' . Str::slug($project->name) . '/' . $this->sourceRepoName;
    }

    public function getSourceUriAttribute(): string
    {
        $provider = (string) $this->attributes['source_provider'];
        $repo = (string) $this->attributes['source_repository'];

        return str_replace('{repo}', $repo, self::SOURCE_PROVIDERS[$provider ?: 'custom']);
    }

    public function getSystemUserAttribute(): ?array
    {
        if (!$this->template()->requiresUser()) {
            return null;
        }

        try {
            /** @var \Servidor\Projects\Project */
            $project = $this->project;
            $username = Str::slug($project->name);

            return SystemUser::findByName($username)->toArray();
        } catch (UserNotFoundException $_) {
            return null;
        }
    }

    public function template(): Template
    {
        $template = (string) ($this->attributes['template'] ?? 'html');

        switch ($this->templatesNamespace . Str::studly(Str::lower($template))) {
            case Html::class:
                return new Html($this);
            case Php::class:
                return new Php($this);
            case Laravel::class:
                return new Laravel($this);
            default:
                throw new Exception("Invalid template '${template}'.");
        }
    }

    public function writeNginxConfig(): void
    {
        $view = $this->template()->nginxTemplate();

        $src = "vhosts/{$this->domain_name}.conf";
        $dst = "/etc/nginx/sites-available/{$this->domain_name}.conf";

        Storage::put($src, $view->with('app', $this)->render());
        exec('sudo cp "' . storage_path('app/' . $src) . '" "' . $dst . '"');
    }
}
