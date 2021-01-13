<?php

namespace Servidor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Servidor\Exceptions\System\UserNotFoundException;
use Servidor\System\User as SystemUser;

class Site extends Model
{
    protected $appends = ['logs'];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'primary_domain',
        'type',
        'source_repo',
        'source_branch',
        'project_root',
        'public_dir',
        'redirect_type',
        'redirect_to',
        'is_enabled',
    ];

    public function getDocumentRootAttribute(): string
    {
        $docroot = $this->project_root;

        if ('laravel' === $this->type) {
            $docroot .= $this->public_dir;
        }

        return $docroot;
    }

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

    public function readLog(string $log): string
    {
        $path = $this->logs[$log]['path'];

        if (!Str::startsWith($path, '/')) {
            $path = $this->project_root . '/' . $path;
        }

        exec('sudo cat ' . escapeshellarg($path), $file);

        return implode("\n", $file);
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
