<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Task;
use App\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->q;
        if ($keyword) {

            $files = File::where('original_name', 'LIKE', "%$keyword%")
                ->orWhereHas('user', function($query) use($keyword){
                    $query->where('name', 'LIKE', "%$keyword%");
                })->orWhereHas('task', function($query) use($keyword){
                    $query->where('name', 'LIKE', "%$keyword%")->where('status', 1);
                })->with('task', 'user')->orderBy('updated_at', 'desc')->where('status_approve', 3)->get();

            $tasks = Task::with(['responsible_person', 'user'])
                ->whereHas('responsible_person', function($q) use($keyword){
                    $q->where('name', 'LIKE', "%$keyword%");
                })
                ->orWhere('name', 'LIKE', "%$keyword%")->orderBy('updated_at', 'desc')->get();

            $ret['files'] = $files;
            $ret['tasks'] = $tasks;

            return view('searches.index', $ret);
        }

        $files = File::with('task', 'user')->where('status_approve', 3)->orWhere('is_default', 1)->orderBy('updated_at', 'desc')->limit(10)->get();
        $tasks = Task::with(['responsible_person', 'user'])->orderBy('updated_at', 'desc')->limit(10)->get();

        $ret['files'] = $files;
        $ret['tasks'] = $tasks;
        return view('searches.index', $ret);
    }

    public function search(Request $request)
    {
        $keyword = $request->q;

        $files = File::where('original_name', 'LIKE', "%$keyword%")->where('status_approve', 3)
            ->orWhereHas('user', function($query) use($keyword){
                $query->where('name', 'LIKE', "%$keyword%");
            })->orWhereHas('task', function($query) use($keyword){
                $query->where('name', 'LIKE', "%$keyword%")->where('status', 1);
            })->with('task', 'user')->orderBy('updated_at', 'desc')->get();

        $tasks = Task::with(['responsible_person', 'user'])
            ->whereHas('responsible_person', function($q) use($keyword){
                $q->where('name', 'LIKE', "%$keyword%");
            })
            ->orWhere('name', 'LIKE', "%$keyword%")->orderBy('updated_at', 'desc')->get();

        return response()->json(["files" => $files, "tasks" => $tasks], 200);

    }
}
