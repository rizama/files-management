<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            if ($this->user->role->code != 'superadmin') {
                abort(404);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $roles = Role::all();
        $ret['roles'] = $roles;

        return view('roles.index', $ret);
    }

    public function edit($role_id)
    {
        // $user->load('role');
        try {
            $decrypted_id = decrypt($role_id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        $role = Role::where('id', $decrypted_id)->firstOrFail();
        $ret['role'] = $role;
        return view('roles.edit', $ret);
    }

    public function update(Request $request, $role_id)
    {
        try {

            $data = $request->except('_method','_token','submit');

            try {
                $decrypted_id = decrypt($role_id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $validator = Validator::make($data, [
                'name' => 'required|string',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $role = Role::findOrFail($decrypted_id);
            $role->name = $data['name'];
            $role->description = $data['description'];
            $role->save();

            $request->session()->flash('role.updated', 'Role berhasil diubah!');
            return redirect()->route('roles.index');

        } catch (\Exception $e) {
            if ("The payload is invalid." == $e->getMessage()) {
                return abort(404);
            }
            return abort(500);
        }
    }
}
