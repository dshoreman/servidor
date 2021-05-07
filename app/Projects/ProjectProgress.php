<?php

namespace Servidor\Projects;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @suppressWarnings(PHPMD.LongVariable)
 */
class ProjectProgress implements ShouldBroadcast
{
    use Dispatchable;
    use SerializesModels;

    protected bool $deleteWhenMissingModels = true;

    private Project $project;

    private ProgressStep $step;

    public function __construct(Project $project, ProgressStep $step)
    {
        $this->project = $project;
        $this->step = $step;
    }

    public function broadcastAs(): string
    {
        return 'progress';
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('projects.' . $this->project->id);
    }

    public function broadcastWith(): array
    {
        return [
            'project' => $this->project,
            'step' => $this->step->toArray(),
        ];
    }
}
