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
                    $q->where('is_default', 0)->where('status_approve', 2);
                }])->where('created_by', $id)->get();

                $notif_count = 0;
                foreach ($notif_tasks as $notif_key => $notif) {
                    $notif_count = $notif_count + count($notif->files);
                }
                $view->with('notif_count', $notif_count);
            } else {
                $id = Auth::id();
                $individu_task = Task::with(['responsible_person'])
                    ->whereHas('responsible_person', function ($query) use($id) {
                        $query->where('user_id', $id)->where('status', 1);
                    })
                    ->get();
                
                $general_task = Task::where('assign_to', 'all')->where('status', 1)->get();
                $notif_count = count($individu_task) + count($general_task);
                $view->with('notif_count', $notif_count);
            }
        }
    }
}