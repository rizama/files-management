<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $ret['user'] = $user;
        return view('notifications.index', $ret);
    }
}
