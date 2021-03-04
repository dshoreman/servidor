<?php

use Illuminate\Support\Facades\Route;
use Servidor\Http\Controllers\Auth\Logout;
use Servidor\Http\Controllers\DatabaseController;
use Servidor\Http\Controllers\FallbackController;
use Servidor\Http\Controllers\Files\CreateNode;
use Servidor\Http\Controllers\Files\DeletePath;
use Servidor\Http\Controllers\Files\EditFile;
use Servidor\Http\Controllers\Files\ListOrShowPath;
use Servidor\Http\Controllers\Files\MovePath;
use Servidor\Http\Controllers\Projects\Applications\PullCode;
use Servidor\Http\Controllers\Projects\Applications\ViewLog;
use Servidor\Http\Controllers\Projects\CreateProject;
use Servidor\Http\Controllers\Projects\ListProjects;
use Servidor\Http\Controllers\Projects\RemoveProject;
use Servidor\Http\Controllers\Projects\UpdateProject;
use Servidor\Http\Controllers\System\Git\ListBranches;
use Servidor\Http\Controllers\System\GroupsController;
use Servidor\Http\Controllers\System\UsersController;
use Servidor\Http\Controllers\SystemInformationController;
use Servidor\Http\Controllers\User\ShowProfile;

Route::post('logout', Logout::class);

Route::middleware('auth:api')->group(function (): void {
    Route::name('projects.')->prefix('/projects')->group(function (): void {
        Route::get('/', ListProjects::class);
        Route::post('/', CreateProject::class);
        Route::put('{project}', UpdateProject::class);
        Route::prefix('{project}/apps/{app}')->group(function (): void {
            Route::post('pull', PullCode::class);
        });
        Route::get('{project}/logs/{log}.app-{app}.log', ViewLog::class);
        Route::delete('{project}', RemoveProject::class);
    });

    Route::resource('databases', DatabaseController::class, [
        'only' => ['index', 'store'],
    ]);

    Route::get('files', ListOrShowPath::class);
    Route::put('files', EditFile::class);
    Route::post('files', CreateNode::class);
    Route::post('files/rename', MovePath::class);
    Route::delete('files', DeletePath::class);

    Route::name('system')->prefix('/system')->group(function (): void {
        Route::get('git/branches', ListBranches::class);

        Route::resource('groups', GroupsController::class, [
            'only' => ['index', 'store', 'update', 'destroy'],
        ]);

        Route::resource('users', UsersController::class, [
            'only' => ['index', 'store', 'update', 'destroy'],
        ]);
    });

    Route::get('system-info', SystemInformationController::class);
    Route::get('user', ShowProfile::class);
});

Route::any('/{all?}', [FallbackController::class, 'api'])->where('all', '.*');
