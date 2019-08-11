<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'Auth\LoginController@login');
Route::post('register', 'Auth\RegisterController@register');

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('logout', 'Auth\LoginController@logout');

    Route::resource('sites', 'SiteController', [
        'only' => ['index', 'store', 'update', 'destroy'],
    ]);

    Route::resource('files', 'FileController', [
        'only' => ['index'],
    ]);

    // Shitty hack because Laravel decodes the entire URL and
    // treats encoded slashes the exact same as a raw '/'.
    // Taylor's answer? "Don't use slash". What a moron.
    Route::get('/files/{filepath}', 'FileController@show')
        ->where('filepath', '.*');

    Route::name('system')->prefix('/system')->namespace('System')->group(function () {
        Route::resource('groups', 'GroupsController', [
            'only' => ['index', 'store', 'update', 'destroy'],
        ]);

        Route::resource('users', 'UsersController', [
            'only' => ['index', 'store', 'update', 'destroy'],
        ]);
    });

    Route::get('system-info', 'SystemInformationController');
});

Route::any('/{all?}', function () {
    abort(404);
})->where('all', '.*');
