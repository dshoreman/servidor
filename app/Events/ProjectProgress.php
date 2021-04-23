<?php

namespace Servidor\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Servidor\Projects\Project;

class ProjectProgress implements ShouldBroadcast
{
    use Dispatchable;
    use SerializesModels;

    public Project $project;

    public string $text;

    public function __construct(Project $project, string $text)
    {
        $this->project = $project;
        $this->text = $text;
    }

    public function broadcastAs(): string
    {
        return 'progress';
    }

    public function broadcastOn(): Channel
    {
        return new Channel('projects.' . $this->project->id);
    }
}
