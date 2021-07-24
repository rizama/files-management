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
        return view('users', ['users' => User::all()]);
    }

    public function insert(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|string|unique:users',
                'password' => 'required|string|min:5',
            ]);
    
            if ($validator->fails()) {
                return $validator->errors();
            }

            dd($request->all());

        } catch (\Exception $e) {
            dd($e);
        }
    } 
}
