<?php

namespace Servidor\Projects;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Servidor\Projects\Services\LogFile;
use Servidor\Projects\Services\ProjectServiceSaved;
use Servidor\Projects\Services\ProjectServiceSaving;
use Servidor\Projects\Services\Templates\Html;
use Servidor\Projects\Services\Templates\Laravel;
use Servidor\Projects\Services\Templates\Php;
use Servidor\Projects\Services\Templates\Template;
use Servidor\System\User as SystemUser;
use Servidor\System\Users\UserNotFound;

/**
 * A ProjectService is a Project component for websites, apps or server processes.
 *
 * @property int         $id
 * @property int         $project_id
 * @property string      $template
 * @property string      $domain_name
 * @property ?Collection $config
 * @property ?Carbon     $created_at
 * @property ?Carbon     $updated_at
 * @property string      $document_root
 * @property array       $logs
 * @property string      $source_repo_name
 * @property string      $source_root
 * @property string      $source_uri
 * @property ?array      $system_user
 * @property Project     $project
 *
 * @method static Builder|ProjectService query()
 * @method static Builder|ProjectService whereCreatedAt($value)
 * @method static Builder|ProjectService whereDomainName($value)
 * @method static Builder|ProjectService whereId($value)
 * @method static Builder|ProjectService whereProjectId($value)
 * @method static Builder|ProjectService whereTemplate($value)
 * @method static Builder|ProjectService whereUpdatedAt($value)
 */
class ProjectService extends Model
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

    protected $casts = [
        'config' => AsCollection::class,
    ];

    protected $dispatchesEvents = [
        'saving' => ProjectServiceSaving::class,
        'saved' => ProjectServiceSaved::class,
    ];

    protected $fillable = [
        'template',
        'domain_name',
        'include_www',
        'config',
    ];

    protected $table = 'project_services';

    /** @var string */
    private $templatesNamespace = 'Servidor\Projects\Services\Templates\\';

    public function getDocumentRootAttribute(): string
    {
        return $this->sourceRoot . $this->template()->publicDir();
    }

    public function checkNginxData(): void
    {
        $this->template()->checkNginxData();
    }

    public function getLogsAttribute(): array
    {
        return array_map(static fn (LogFile $log): string => $log->getTitle(), $this->logs());
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
        if (!$this->config || !$this->config->has('source')) {
            return '';
        }

        $source = (array) $this->config->get('source');
        $repo = (string) $source['repository'];
        $match = mb_strpos($repo, '/');

        return false === $match ? $repo
            : mb_substr($repo, $match + 1);
    }

    public function getSourceRootAttribute(): string
    {
        $tpl = $this->template();

        if ($tpl->requiresUser() && $this->systemUser) {
            // TODO: If user doesn't exist, this should throw UserNotFoundException
            //  (or some similar error) and NOT just default to a root-based path!
            return ((string) $this->systemUser['dir']) . '/' . $this->sourceRepoName;
        }

        /** @var \Servidor\Projects\Project $project */
        $project = $this->project;

        return '/var/www/' . Str::slug($project->name) . '/' . $this->sourceRepoName;
    }

    public function getSourceUriAttribute(): string
    {
        if (!$this->config || !$this->config->has('source')) {
            return '';
        }

        $source = (array) $this->config->get('source');
        $provider = (string) $source['provider'];
        $repo = (string) $source['repository'];

        return str_replace('{repo}', $repo, self::SOURCE_PROVIDERS[$provider ?: 'custom']);
    }

    public function getSystemUserAttribute(): ?array
    {
        if (!$this->template()->requiresUser()) {
            return null;
        }

        try {
            /** @var \Servidor\Projects\Project $project */
            $project = $this->project;
            $username = Str::slug($project->name);

            return SystemUser::findByName($username)->toArray();
        } catch (UserNotFound $_) {
            return null;
        }
    }

    public function getType(): string
    {
        return 'application';
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
                throw new Exception("Invalid template '{$template}'.");
        }
    }
}
