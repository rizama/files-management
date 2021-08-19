<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            if ($this->user->role->code != 'superadmin') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $users = User::with('role')->get();
        $ret['users'] = $users;

        return view('users.index', $ret);
    }

    public function create()
    {
        try {
            $roles = Role::all();
            $ret['roles'] = $roles;
            return view('users.create', $ret);
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    public function store(Request $request)
    {
        try {
            $faker = \Faker\Factory::create();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'nullable|string|unique:users',
                'username' => 'required|string|unique:users',
                'password' => 'required|string|min:5',
                'role_id' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $user = new User;
            $user->role_id = $request->role_id;
            $user->name = $request->name;
            if ($request->email) {
                $user->email = $request->email;
            } else {
                $user->email = $faker->unique()->safeEmail;
            }
            $user->username = $request->username;
            $user->password = Hash::make($request->password);
            $user->save();

            $request->session()->flash('user.created', 'Pengguna telah dibuat!');
            return redirect()->route('users.index');

        } catch (\Exception $e) {
            return abort(500);
        }
    }

    public function show(User $user)
    {
        // $ret['user'] = $user;
        // return view('', $ret);
    }

    public function edit($user_id)
    {
        try {
            $decrypted_id = decrypt($user_id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        $user = User::with('role')->where('id', $decrypted_id)->firstOrFail();
        $roles = Role::all();
        $ret['roles'] = $roles;
        $ret['user'] = $user;
        return view('users.edit', $ret);
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

            $validator = Validator::make($data, [
                'name' => 'required|string',
                'password' => 'nullable|string',
                'email' => [
                    'nullable',
                    'string',
                    Rule::unique('users', 'email')->ignore($decrypted_id),
                ],
                'username' => [
                    'required',
                    'string',
                    Rule::unique('users', 'username')->ignore($decrypted_id),
                ],
                'role_id' => 'required',
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $user = User::findOrFail($decrypted_id);

            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->username = $data['username'];
            if ($data['password']) {
                $user->password = Hash::make($data['password']);
            }
            $user->role_id = $data['role_id'];
            $user->save();

            $request->session()->flash('user.updated', 'Pengguna telah diubah!');
            return redirect()->route('users.index');

        } catch (\Exception $e) {
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
            
            $request->session()->flash('user.deleted', 'Pengguna telah dihapus!');
            return redirect()->route('users.index');
            
        } catch (\Exception $e) {
            if ("The payload is invalid." == $e->getMessage()) {
                return abort(404);
            }
            return abort(500);
        }
    }
}
