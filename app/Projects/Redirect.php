<?php

namespace Servidor\Projects;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Servidor\Projects\Redirects\ProjectRedirectSaved;
use Servidor\Projects\Redirects\ProjectRedirectSaving;

/**
 * A Redirect is similar to a Project's Application, but it has no files
 * and is more strict in that it can only redirect a domain/rule elsewhere.
 *
 * @property int         $id
 * @property int         $project_id
 * @property string      $domain_name
 * @property string      $target
 * @property int         $type
 * @property ?Collection $config
 * @property ?Carbon     $created_at
 * @property ?Carbon     $updated_at
 * @property Project     $project
 *
 * @method static Builder|Redirect query()
 * @method static Builder|Redirect whereCreatedAt($value)
 * @method static Builder|Redirect whereDomainName($value)
 * @method static Builder|Redirect whereId($value)
 * @method static Builder|Redirect whereProjectId($value)
 * @method static Builder|Redirect whereTarget($value)
 * @method static Builder|Redirect whereType($value)
 * @method static Builder|Redirect whereUpdatedAt($value)
 */
class Redirect extends Model
{
    use RequiresNginxData;

    protected $casts = [
        'config' => AsCollection::class,
    ];

    protected $dispatchesEvents = [
        'saving' => ProjectRedirectSaving::class,
        'saved' => ProjectRedirectSaved::class,
    ];

    protected $fillable = [
        'domain_name',
        'include_www',
        'config',
    ];

    protected array $requiredNginxData = [
        'config.redirect.target' => 'target',
        'domain_name' => 'domain name',
    ];

    protected $table = 'project_redirects';

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getType(): string
    {
        return 'redirect';
    }
}
