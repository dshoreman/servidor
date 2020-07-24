<?php

namespace Servidor;

use Illuminate\Database\Eloquent\Model;
use Servidor\Events\SiteUpdated;
use Servidor\Exceptions\System\UserNotFoundException;
use Servidor\System\User as SystemUser;

class Site extends Model
{
    protected $appends = ['logs'];

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

    public function getLogsAttribute(): array
    {
        $defaultPhp = sprintf('/var/log/php%d.%d-fpm.log', PHP_MAJOR_VERSION, PHP_MINOR_VERSION);
        $php = ['name' => 'PHP Error Log', 'path' => ini_get('error_log') ?: $defaultPhp];
        $laravel = ['name' => 'Laravel Log', 'path' => 'storage/logs/laravel.log'];

        switch ($this->attributes['type'] ?? '') {
            case 'laravel':
                return compact('php', 'laravel');
            case 'php':
                return compact('php');
        }

        return [];
    }

    /**
     * @return int|array|null
     */
    public function getSystemUserAttribute()
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

    public function setSystemUserAttribute(int $uid): void
    {
        $this->attributes['system_user'] = $uid;
    }
}
