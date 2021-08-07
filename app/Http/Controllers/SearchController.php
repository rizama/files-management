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
             // Person
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

            $ret['files'] = $files;
            $ret['tasks'] = $tasks;
            // return response()->json(["files" => $files, "tasks" => $tasks], 200);
            return view('searches.index', $ret);
        }
        return view('searches.index');
    }

    public function search(Request $request)
    {
        $keyword = $request->q;

        // ->where('name', 'LIKE', "%$keyword%")
        
        // Person
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
