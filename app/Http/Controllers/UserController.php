<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\User;
use Auth;

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
                'role_id' => 'required',
            ]);
    
            if ($validator->fails()) {
                return $validator->errors();
            }

            $user = new User;
            $user->role_id = $request->role_id;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            dd(encrypt($user->id));

        } catch (\Exception $e) {
            dd($e);
            return abort(500);
        }
    }

    public function update(Request $request, $user_id)
    {   
        try {

            $data = $request->except('_method','_token','submit');

            try {
                $decrypted_id = decrypt($user_id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $validator = \Validator::make($data, [
                'name' => 'required|string',
                'email' => [
                    'required',
                    'string',
                    Rule::unique('users', 'email')->ignore($decrypted_id),
                ],
                'role_id' => 'required',
            ]);
    
            if ($validator->fails()) {
                return $validator->errors();
            }

            $user = User::findOrFail($decrypted_id);

            $user->update($data);

            dd($user);

        } catch (\Exception $e) {
            dd($e);
            if ("The payload is invalid." == $e->getMessage()) {
                return abort(404);
            }
            return abort(500);
        }
    }

    public function destroy(Request $request, $user_id)
    {
        try {
            try {
                $decrypted_id = decrypt($user_id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $user = User::findOrFail($decrypted_id);
            $user->delete();
            dd($user);
            
        } catch (\Exception $e) {
            dd($e);
            if ("The payload is invalid." == $e->getMessage()) {
                return abort(404);
            }
            return abort(500);
        }
    }
}
