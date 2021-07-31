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

include('frontend.php');
include('backend.php');