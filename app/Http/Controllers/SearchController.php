<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Task;
use App\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index()
    {
        return view('searches.index');
    }

    public function search(Request $request)
    {
        $keyword = $request->q;

        // Person
        $files = File::where('original_name', 'LIKE', "%$keyword%")
            ->orWhereHas('user', function($query) use($keyword){
                $query->where('name', 'LIKE', "%$keyword%");
            })->orWhereHas('task', function($query) use($keyword){
                $query->where('name', 'LIKE', "%$keyword%");
            })->with('task', 'user')->orderBy('updated_at', 'desc')->get();
        
        $tasks = Task::where('name', 'LIKE', "%$keyword%")->with('user')->orderBy('updated_at', 'desc')->get();
        
        return response()->json(["files" => $files, "tasks" => $tasks], 200);

    }
}
