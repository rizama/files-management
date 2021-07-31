<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => 'auth'
    ],
    function () {
        // Route
        Route::get('/users', 'UserController@index')->name('users.index');
        Route::get('/users/create', 'UserController@create')->name('users.create');
        Route::get('/users/edit/{user_id}', 'UserController@edit')->name('users.edit');

        Route::get('/roles', 'RoleController@index')->name('roles.index');
        Route::get('/roles/edit/{role}', 'RoleController@edit')->name('roles.edit');

        //Tasks
        Route::get('/tasks', 'TaskController@index')->name('tasks.index');
        Route::get('/tasks/create', 'TaskController@create')->name('tasks.create');
        Route::get('/tasks/edit/{id}', 'TaskController@edit')->name('tasks.edit');
});