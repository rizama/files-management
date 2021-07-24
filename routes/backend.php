<?php

// User Module
Route::group(
    [
        'middleware' => 'auth'
    ],
    function () {
        Route::post('/users', 'UserController@insert');
        Route::put('/users/{user_id}', 'UserController@update');
        Route::delete('/users/{user_id}', 'UserController@destroy');
});
