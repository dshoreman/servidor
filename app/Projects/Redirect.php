<?php

namespace Servidor\Projects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Servidor\Traits\TogglesNginxConfigs;

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
        return $this->attributes['domain_name'] ?? '';
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
