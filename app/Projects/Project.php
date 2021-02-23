<?php

namespace Servidor\Projects;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A Project is a container for one or more Applications or Redirects.
 *
 * @property int                      $id
 * @property string                   $name
 * @property bool                     $is_enabled
 * @property ?Carbon                  $created_at
 * @property ?Carbon                  $updated_at
 * @property Collection|Application[] $applications
 * @property ?int                     $applications_count
 * @property Collection|Redirect[]    $redirects
 * @property ?int                     $redirects_count
 *
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

    protected $table = 'projects';

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function redirects(): HasMany
    {
        return $this->hasMany(Redirect::class);
    }
}
