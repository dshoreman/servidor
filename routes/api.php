<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Servidor\Http\Controllers\Auth\Login;
use Servidor\Http\Controllers\Auth\Logout;
use Servidor\Http\Controllers\Auth\Register;
use Servidor\Http\Controllers\Databases\CreateDatabase;
use Servidor\Http\Controllers\Databases\ListDatabases;
use Servidor\Http\Controllers\Databases\ShowDatabase;
use Servidor\Http\Controllers\FallbackController;
use Servidor\Http\Controllers\Files\CreateNode;
use Servidor\Http\Controllers\Files\DeletePath;
use Servidor\Http\Controllers\Files\EditFile;
use Servidor\Http\Controllers\Files\ListOrShowPath;
use Servidor\Http\Controllers\Files\MovePath;
use Servidor\Http\Controllers\Projects\CreateProject;
use Servidor\Http\Controllers\Projects\CreateProjectRedirect;
use Servidor\Http\Controllers\Projects\CreateProjectService;
use Servidor\Http\Controllers\Projects\ListProjects;
use Servidor\Http\Controllers\Projects\RemoveProject;
use Servidor\Http\Controllers\Projects\Services\PullCode;
use Servidor\Http\Controllers\Projects\Services\ViewLog;
use Servidor\Http\Controllers\Projects\UpdateProject;
use Servidor\Http\Controllers\System\Git\ListBranches;
use Servidor\Http\Controllers\System\GroupsController;
use Servidor\Http\Controllers\System\UsersController;
use Servidor\Http\Controllers\SystemInformationController;
use Servidor\Http\Controllers\User\ShowProfile;

Route::post('register', Register::class);
Route::middleware('web')->name('login')->post('session', Login::class);
Route::middleware('web')->delete('session', Logout::class);
Broadcast::routes(['middleware' => 'auth:api']);

Route::middleware('auth:api')->group(static function (): void {
    Route::name('projects.')->prefix('/projects')->group(static function (): void {
        Route::get('/', ListProjects::class);
        Route::post('/', CreateProject::class);
        Route::put('{project}', UpdateProject::class);

        Route::prefix('{project}/services')->group(static function (): void {
            Route::post('/', CreateProjectService::class);
            Route::post('{service}/pull', PullCode::class);
        });
        Route::prefix('{project}/redirects')->group(static function (): void {
            Route::post('/', CreateProjectRedirect::class);
        });

        Route::get('{project}/logs/{log}.service-{service}.log', ViewLog::class);
        Route::delete('{project}', RemoveProject::class);
    });

    Route::name('databases.')->prefix('/databases')->group(static function (): void {
        Route::get('/', ListDatabases::class);
        Route::post('/', CreateDatabase::class);
        Route::get('{database}', ShowDatabase::class);
    });

    Route::get('files', ListOrShowPath::class);
    Route::put('files', EditFile::class);
    Route::post('files', CreateNode::class);
    Route::post('files/rename', MovePath::class);
    Route::delete('files', DeletePath::class);

    Route::name('system')->prefix('/system')->group(static function (): void {
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
