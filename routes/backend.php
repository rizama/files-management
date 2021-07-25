<?php

// User Module
Route::group(
    [
        'middleware' => 'auth'
    ],
    function () {
        Route::post('/users', 'UserController@store');
        Route::put('/users/{user_id}', 'UserController@update');
        Route::delete('/users/{user_id}', 'UserController@destroy');

        Route::post('/tasks', 'TaskController@store');
        Route::put('/tasks/{task_id}', 'TaskController@update');
        Route::delete('/tasks/{task_id}', 'TaskController@destroy');

        Route::get('/files/get-local', 'FileController@get_local_file');
        Route::get('/files/get-s3', 'FileController@get_s3_file');
});
