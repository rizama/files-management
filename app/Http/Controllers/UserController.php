<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\User;

class UserController extends Controller
{

    public function index(Request $request)
    {
        return User::all();
    }

    public function insert(Request $request)
    {
        dd($request->all());
    } 
}
