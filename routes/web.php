<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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

Route::get('/encrypt', function (Request $request) {
    return encrypt($request->id); 
});

Route::get('/decrypt', function (Request $request) {
    return decrypt($request->id); 
});

Auth::routes();

Route::group(
    [
        'middleware' => 'auth'
    ],
    function () {
        Route::get('/dashboard', 'HomeController@index')->name('dashboard');

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
        Route::get('mytasks', 'TaskController@my_task')->name('tasks.my_task');
        Route::get('/tasks/show/{id}', 'TaskController@show')->name('tasks.show');
        Route::get('/tasks/create', 'TaskController@create')->name('tasks.create');
        Route::post('/tasks', 'TaskController@store')->name('tasks.store');
        Route::get('/tasks/edit/{id}', 'TaskController@edit')->name('tasks.edit');
        Route::put('/tasks/{id}', 'TaskController@update')->name('tasks.update');
        Route::get('/tasks/{id}', 'TaskController@destroy')->name('tasks.destroy');
        Route::post('/tasks/{id}/send_file', 'TaskController@send_file_task')->name('tasks.send_file');
        Route::post('/tasks/{id}/send_note', 'TaskController@send_note_task')->name('tasks.send_note');
        Route::post('/tasks/{id}/approve/file', 'TaskController@approve')->name('tasks.approve');
        Route::post('/tasks/{id}/reject/file', 'TaskController@reject')->name('tasks.reject');
        Route::get('/tasks/{id}/approve/task', 'TaskController@approve_task')->name('tasks.approve_task');
        Route::get('notifications', 'TaskController@notif')->name('tasks.notif');

        // Files Route
        Route::get('/files/get-local', 'FileController@get_local_file');
        Route::get('/files/get-s3', 'FileController@get_s3_file');

        //Searches
        Route::get('/search', 'SearchController@index')->name('search.index');
        // Route::get('/search', 'SearchController@search')->name('do.search');  
        
        // File
        Route::get('/download', 'FileController@download_file')->name('download');
        Route::get('/files/delete/{id}', 'FileController@destroy')->name('file.delete');
        
        // Categories
        Route::get('categories', 'CategoryController@index')->name('categories.index');
        Route::get('categories/create', 'CategoryController@create')->name('categories.create');
        Route::post('categories', 'CategoryController@store')->name('categories.store');
        Route::get('categories/edit/{id}', 'CategoryController@edit')->name('categories.edit');
        Route::put('categories/{id}', 'CategoryController@update')->name('categories.update');
        Route::get('categories/{id}', 'CategoryController@destroy')->name('categories.destroy');

        // File Public
        Route::get('file_publics/search', 'FilePublicController@search')->name('search.public');
        Route::get('file_publics/download', 'FilePublicController@download_file')->name('download.public');
        Route::get('file_publics', 'FilePublicController@index')->name('file_publics.index');
        Route::get('file_publics/create', 'FilePublicController@create')->name('file_publics.create');
        Route::post('file_publics', 'FilePublicController@store')->name('file_publics.store');
        Route::get('file_publics/edit/{id}', 'FilePublicController@edit')->name('file_publics.edit');
        Route::put('file_publics/{id}', 'FilePublicController@update')->name('file_publics.update');
        Route::get('file_publics/{id}', 'FilePublicController@destroy')->name('file_publics.destroy');

});