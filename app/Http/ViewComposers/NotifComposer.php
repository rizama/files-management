<?php

namespace App\Http\ViewComposers;

use App\Models\File;
use App\Models\Task;
use Illuminate\View\View;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class NotifComposer
{
    public function compose(View $view)
    {
        if (Auth::id()) {
            if (Auth::user()->role->code == 'level_1') {
                $id = Auth::id();
                $notif_tasks = Task::with(['files' => function($q){
                    $q->where('is_default', 0)->where('type', 'internal')->where('status_approve', 2)->orderBy('created_at', 'desc');
                }])->where('created_by', $id)->get();

                $notif_count = 0;
                $notif_content = [];
                foreach ($notif_tasks as $notif) {
                    if (count($notif->files)) {
                        if ($notif->is_confirm_all == 0) {
                            $notif_count = $notif_count + 1;
                            $notif_content[] = $notif->files[0]->load('user', 'task');
                        } else {
                            $notif_count = $notif_count + count($notif->files);
                            foreach ($notif->files as $key => $file) {
                                $notif_content[] = $file->load('user', 'task');
                            }
                        }
                    }
                }
                
                $contents = [];
                foreach ($notif_content as $key => $content) {
                    $temp = [
                        "info" => "Dokumen Belum Dikonfirmasi",
                        "id" => $content->id,
                        "file" => $content->original_name,
                        "created_at" => $content->created_at,
                        "user" => $content->user->name,
                        "task_id" => $content->task->id,
                    ];

                    $contents[] = $temp;
                }

                $view->with('notif_count', $notif_count);
                $view->with('notif_content', $contents);
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
                $notif_count = count($individu_task) + count($general_task);

                $merged = $individu_task->merge($general_task);
                $merged->load('user', 'files');

                $contents = [];
                foreach ($merged as $key_content => $content) {
                    $uploader = [];
                    foreach ($content->files as $key => $file_) {
                        $uploader[] = $file_->created_by;
                    }

                    if (!in_array(Auth::id(), $uploader)) {
                        $temp = [
                            "info" => "Task Belum Dikerjakan",
                            "id" => $content->id,
                            "task" => $content->name,
                            "created_at" => $content->created_at,
                            "creator" => $content->user->name,
                            "task_id" => $content->id,
                        ];
                        $contents[] = $temp;
                    }
                }

                $view->with('notif_count', count($contents));
                $view->with('notif_content', $contents);
            }
        }
    }
}