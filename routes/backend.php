<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => 'auth'
    ],
    function () {
        // User Module
        Route::post('/users', 'UserController@store')->name('users.store');
        Route::put('/users/{user_id}', 'UserController@update')->name('users.update');
        Route::get('/users/{user_id}', 'UserController@destroy')->name('users.destroy');

        Route::get('/tasks', 'TaskController@index');
        Route::post('/tasks', 'TaskController@store');
        Route::put('/tasks/{task_id}', 'TaskController@update');
        Route::delete('/tasks/{task_id}', 'TaskController@destroy');

        Route::get('/files/get-local', 'FileController@get_local_file');
        Route::get('/files/get-s3', 'FileController@get_s3_file');

        Route::put('/roles/{role_id}', 'RoleController@update')->name('roles.update');

        Route::get('/search', 'SearchController@search')->name('do.search');
});
