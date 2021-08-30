<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifController extends Controller
{
    public function index()
    {
        if (Auth::id()) {
            if (Auth::user()->role->code == 'level_1') {
                $id = Auth::id();
                $tasks = Task::with(['files' => function($q){
                    $q->where('is_default', 0)->where('type', 'internal')->where('status_approve', 2)->orderBy('created_at', 'desc');
                }])->where('created_by', $id)->get();

                $contents_count = 0;
                $contents = [];
                foreach ($tasks as $task) {
                    $contents_count = $contents_count + count($task->files);
                    if (count($task->files)) {
                        if ($task->is_confirm_all == 0) {
                            $contents[] = $task->files[0]->load('user', 'task');
                        } else {
                            foreach ($task->files as $key => $file) {
                                $contents[] = $file->load('user', 'task');
                            }
                        }
                    }
                }
                $ret['contents'] = $contents;
                $ret['contents_count'] = $contents_count;
                return view('notifications.index', $ret);

            } else {
                $id = Auth::id();
                $individu_task = Task::with(['responsible_person'])
                    ->whereHas('responsible_person', function ($query) use($id) {
                        $query->where('user_id', $id)->where('status', 1);
                    })
                    ->get();
                
                $general_task = Task::where('assign_to', 'all')->where('status', 1)->get();
                if (Auth::user()->role->code == 'superadmin') {
                    $general_task = [];
                }

                $merged = $individu_task->merge($general_task);
                $merged->load('user', 'files');

                $contents = [];
                foreach ($merged as $key_content => $content) {
                    $uploader = [];
                    foreach ($content->files as $key => $file_) {
                        $uploader[] = $file_->created_by;
                    }
                    if (!in_array(Auth::id(), $uploader)) {
                        $contents[] = $content;
                    }
                }

                $ret['contents'] = $contents;
                $ret['contents_count'] = count($contents);
                return view('notifications.index', $ret);

            }
        }
    }
}
