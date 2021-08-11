<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\User;
use Illuminate\Http\Request;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            if ($this->user->role->code == 'superadmin') {
                abort(404);
            }

            return $next($request);
        });
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $tasks = Task::with(['files' => function($q){
            $q->where('is_default', 0)->orderBy('updated_at', 'desc');
        }])->get();

        $done = 0;
        $waiting = 0;
        $progress = 0;
        foreach ($tasks as $index => $task) {
            if ($task->status == 3) {
                $done++;
            } else {
                if (count($task->files)) {
                    if ($task->files[0]->status_approve == 2) {
                        $waiting++;
                    }
                    if (($task->files[0]->status_approve == 4)) {
                        $progress++;
                    }
                    if (($task->files[0]->status_approve == 3)) {
                        $progress++;
                    }
                } else {
                    if ($task->status == 1) {
                        $progress++;
                    }
                }
            }   
        }

        $users = User::with(['responsible_tasks', 'role', 'files' => function($q){
                $q->with('task')->where('is_default', 0)->orderBy('created_at', 'desc');
            }])
            ->whereHas('role', function($query){
                $query->where('code', '!=', 'superadmin')->where('code', '!=', 'level_1');
            })
            ->get();

        $res['task_total'] = count($tasks);
        $res['task_done'] = $done;
        $res['task_waiting'] = $waiting;
        $res['task_progress'] = $progress;
        $res['users'] = $users;
        return view('home', $res);
    }
}
