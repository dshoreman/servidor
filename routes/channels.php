<?php

use Illuminate\Support\Facades\Broadcast;
use Servidor\Projects\Project;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', static fn ($user, $id) => (int) $user->id === (int) $id);

Broadcast::channel(
    'projects.{project}',
    static fn ($user, Project $project) => $project->id && $user->id,
    ['guards' => ['api']],
);
