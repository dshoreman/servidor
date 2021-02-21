<?php

namespace Servidor\Projects;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Servidor\Traits\TogglesNginxConfigs;

/**
 * A Redirect is similar to a Project's Application, but it has no files
 * and is more strict in that it can only redirect a domain/rule elsewhere.
 *
 * @property int     $id
 * @property int     $project_id
 * @property string  $domain_name
 * @property string  $target
 * @property int     $type
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property Project $project
 *
 * @method static Builder|Redirect query()
 * @method static Builder|Redirect whereCreatedAt($value)
 * @method static Builder|Redirect whereDomainName($value)
 * @method static Builder|Redirect whereId($value)
 * @method static Builder|Redirect whereProjectId($value)
 * @method static Builder|Redirect whereTarget($value)
 * @method static Builder|Redirect whereType($value)
 * @method static Builder|Redirect whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Redirect extends Model implements Domainable
{
    use TogglesNginxConfigs;

    protected $fillable = [
        'domain_name',
        'type',
        'target',
    ];

    protected $table = 'project_redirects';

    public function domainName(): string
    {
        return (string) $this->attributes['domain_name'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function writeNginxConfig(): void
    {
        /** @var \Illuminate\View\View */
        $view = view('projects.app-templates.redirect');

        $src = "vhosts/{$this->domain_name}.conf";
        $dst = "/etc/nginx/sites-available/{$this->domain_name}.conf";

        Storage::put($src, (string) $view->with('redirect', $this));
        exec('sudo cp "' . storage_path('app/' . $src) . '" "' . $dst . '"');
    }
}
