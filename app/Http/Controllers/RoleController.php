<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

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
}
