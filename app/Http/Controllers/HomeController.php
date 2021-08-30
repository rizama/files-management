<?php

namespace App\Http\Controllers;

use App\Models\File;
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
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            if ($this->user->role->code == 'superadmin' || $this->user->role->code == 'guest') {
                abort(403);
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
            $q->where('is_default', 0)->where('type', 'internal')->orderBy('updated_at', 'desc');
        }])->get();
        
        $total_task = count($tasks);
        $finished = 0;
        $not_yet = 0;
        $progress = 0;
        foreach ($tasks as $index => $task) {
            if ($task->status == 3) {
                $finished++;
            } else {
                if (count($task->files)) {
                    $progress++;
                } else {
                    $not_yet++;
                }
            }   
        }

        $users = User::with(['responsible_tasks', 'role', 'files' => function($q){
                $q->with('task')->where('is_default', 0)->where('type', 'internal')->orderBy('created_at', 'desc');
            }])
            ->whereHas('role', function($query){
                $query->where('code', '!=', 'superadmin')
                    ->where('code', '!=', 'level_1')
                    ->where('code', '!=', 'guest');
            })
            ->get();
        
        $task_general = Task::where('assign_to', 'all')->get();

        $data_task_person = [];
        foreach ($users as $index => $user) {
            $data_task_person[$user->name] = $user->responsible_tasks;
            foreach ($task_general as $key => $general) {
                $data_task_person[$user->name][] = $general;
            }
        }

        $data_task = [];
        foreach ($data_task_person as $name => $tasks) {
            $total = count($data_task_person[$name]);
            $finish = 0;
            $progres = 0;
            foreach ($tasks as $key_task => $task_person) {
                if ($task_person->status == 1) {
                    $progres++;
                } else if ($task_person->status == 3) {
                    $finish++;
                }
            }
            $data_task[$name] = [
                "total" => $total,
                "finish" => $finish,
                "progres" => $progres,
            ];
        }

        foreach ($users as $user) {
            $his_task = $data_task[$user->name];
            $user['task_total'] = $his_task['total'];
            $user['task_finish'] = $his_task['finish'];
            $user['task_progres'] = $his_task['progres'];
        }
        
        $files = File::with('task', 'user')->where('status_approve', 3)->orderBy('created_at', 'desc')->get();

        $res['data_task'] = $data_task;
        $res['task_total'] = $total_task;
        $res['task_done'] = $finished;
        $res['task_not_yet'] = $not_yet;
        $res['task_progress'] = $progress;
        $res['users'] = $users;
        $res['files'] = $files;
        return view('home', $res);
    }
}
