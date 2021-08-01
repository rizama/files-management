<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () 
{   
    if (Auth::user()) {
        return redirect()->route('dashboard');
    }
    return view('auth/login');
});

Route::get('/token', function () {
    return json_encode(csrf_token()); 
});

Auth::routes();

Route::get('/dashboard', 'HomeController@index')->name('dashboard');

Route::group(
    [
        'middleware' => 'auth'
    ],
    function () {
        // User Route
        Route::get('/users', 'UserController@index')->name('users.index');
        Route::get('/users/create', 'UserController@create')->name('users.create');
        Route::post('/users', 'UserController@store')->name('users.store');
        Route::get('/users/edit/{user_id}', 'UserController@edit')->name('users.edit');
        Route::put('/users/{user_id}', 'UserController@update')->name('users.update');
        Route::get('/users/{user_id}', 'UserController@destroy')->name('users.destroy');

        // Roles Route
        Route::get('/roles', 'RoleController@index')->name('roles.index');
        Route::get('/roles/edit/{role}', 'RoleController@edit')->name('roles.edit');
        Route::put('/roles/{role_id}', 'RoleController@update')->name('roles.update');

        // Tasks Route
        Route::get('/tasks', 'TaskController@index')->name('tasks.index');
        Route::get('/tasks/show/{id}', 'TaskController@show')->name('tasks.show');
        Route::get('/tasks/create', 'TaskController@create')->name('tasks.create');
        Route::post('/tasks', 'TaskController@store')->name('tasks.store');
        Route::get('/tasks/edit/{id}', 'TaskController@edit')->name('tasks.edit');
        Route::put('/tasks/{id}', 'TaskController@update')->name('tasks.update');
        Route::get('/tasks/{id}', 'TaskController@destroy')->name('tasks.destroy');

        // Files Route
        Route::get('/files/get-local', 'FileController@get_local_file');
        Route::get('/files/get-s3', 'FileController@get_s3_file');

        //Searches
        Route::get('/searches', 'SearchController@index')->name('searches.index');
        Route::get('/search', 'SearchController@search')->name('do.search');        
});