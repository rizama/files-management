<?php

// User Module
Route::group(
    [
        'middleware' => 'auth'
    ],
    function () {
        Route::post('/users', 'UserController@insert');
});
