<?php

namespace Servidor;

use Illuminate\Database\Eloquent\Model;
use Servidor\Events\SiteUpdated;
use Servidor\Exceptions\System\UserNotFoundException;
use Servidor\System\User as SystemUser;

class Site extends Model
{
    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    protected $dispatchesEvents = [
        'updated' => SiteUpdated::class,
    ];

    protected $fillable = [
        'name',
        'primary_domain',
        'type',
        'source_repo',
        'source_branch',
        'document_root',
        'redirect_type',
        'redirect_to',
        'is_enabled',
    ];

    public function getSystemUserAttribute(): ?array
    {
        $uid = $this->attributes['system_user'];

        if (!$uid) {
            return null;
        }

        try {
            return SystemUser::find($uid)->toArray();
        } catch (UserNotFoundException $e) {
            return null;
        }
    }
}
