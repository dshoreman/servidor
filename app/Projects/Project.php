<?php

namespace Servidor\Projects;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A Project is a container for one or more ProjectServices.
 *
 * @property int                              $id
 * @property string                           $name
 * @property bool                             $is_enabled
 * @property ?Carbon                          $created_at
 * @property ?Carbon                          $updated_at
 * @property array<ProjectService>|Collection $services
 * @property ?int                             $services_count
 *
 * @method static Project         create()
 * @method static Builder|Project query()
 * @method static Builder|Project whereCreatedAt($value)
 * @method static Builder|Project whereId($value)
 * @method static Builder|Project whereIsEnabled($value)
 * @method static Builder|Project whereName($value)
 * @method static Builder|Project whereUpdatedAt($value)
 */
class Project extends Model
{
    protected $fillable = [
        'name',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    protected $dispatchesEvents = [
        'saved' => ProjectSaved::class,
    ];

    protected $table = 'projects';

    public function services(): HasMany
    {
        return $this->hasMany(ProjectService::class);
    }
}
