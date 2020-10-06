<?php

Route::post('login', 'Auth\LoginController@login');
Route::post('register', 'Auth\RegisterController@register');

Route::middleware('auth:api')->group(function (): void {
    Route::get('/user', 'User\ShowProfile');
    Route::post('logout', 'Auth\LoginController@logout');

    Route::resource('sites', 'SiteController', [
        'only' => ['index', 'store', 'update', 'destroy'],
    ]);
    Route::get('sites/{site}/branches', 'SiteController@branches');
    Route::post('sites/{site}/pull', 'SiteController@pull');
    Route::get('sites/{site}/logs/{log}', 'SiteController@showLog');

    Route::resource('databases', 'DatabaseController', [
        'only' => ['index', 'store'],
    ]);

    Route::get('files', 'FileController@index');
    Route::put('files', 'FileController@update');
    Route::post('files', 'FileController@create');
    Route::post('files/rename', 'FileController@rename');
    Route::delete('files', 'FileController@delete');

    Route::name('system')->prefix('/system')->namespace('System')->group(function (): void {
        Route::resource('groups', 'GroupsController', [
            'only' => ['index', 'store', 'update', 'destroy'],
        ]);

        Route::resource('users', 'UsersController', [
            'only' => ['index', 'store', 'update', 'destroy'],
        ]);
    });

    Route::get('system-info', 'SystemInformationController');
});

Route::any('/{all?}', 'FallbackController@api')->where('all', '.*');
