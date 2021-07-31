<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\Facades\Validator;

use App\Models\Task;
use App\Models\File;
use App\Models\TaskUser;
use Auth;
use Illuminate\Support\Facades\DB;
use App\User;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::with('responsible_person')->get();
        $ret['tasks'] = $tasks;
        $ret['user'] = Auth::user();

        return view('tasks.index', $ret);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $users = User::all();
            $ret['users'] = $users;
            return view('tasks.create', $ret);
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'nullable|string',
                'is_history_active' => 'required',
                'assign_to' => 'nullable',
                'default_file' => 'nullable|mimes:pdf,xls,csv,doc,docx,ppt,pptx,rtf,txt,xlsx',
                'responsible_person' => 'nullable',
                'custom_name' => 'nullable|string'
            ]);
    
            if ($validator->fails()) {
                return $validator->errors();
            }

            $task = new Task;
            $task->created_by = Auth::id();
            $task->name = $request->name;
            $task->description = $request->description ?? '';
            $task->is_history_file_active = (int)$request->is_history_active;
            $task->assign_to = json_encode($request->responsible_person);
            $task->save();

            $responsible_ids = $request->responsible_person;
            $task->responsible_person()->attach($responsible_ids);

            if ($request->hasFile('default_file')) {
                $this->upload_to_s3($task, $request->file('default_file'), $request->custom_name);
            }

            DB::commit();

            return redirect()->route('tasks.create');

        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return abort(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $decrypted_id = decrypt($id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        $task = Task::where('id', $decrypted_id)->firstOrFail();
        $users = User::all();
        $ret['users'] = $users;
        $ret['task'] = $task;
        return view('tasks.edit', $ret);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->except('_method','_token','submit');

            try {
                $decrypted_id = decrypt($id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'required|string',
                'is_history_active' => 'required',
                'assign_to' => 'nullable|string',
                'default_file' => 'nullable|mimes:pdf,xlx,csv,doc,docx,ppt,pptx,rtf,txt',
                'responsible_person' => 'nullable',
                'custom_name' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }

            $task = Task::with('files')->findOrFail($decrypted_id);

            if ($request->hasFile('default_file')) {
                if (!$request->is_history_active) {

                    // Delete Old File
                    $old_file = $task->files->last();
                    $old_name_file = $old_file->new_name;
                    $exists = Storage::disk('s3')->exists('files/'.$old_name_file);
                    if ($exists) {
                        Storage::disk('s3')->delete('files/'.$old_name_file);
                    }
                    $old_file->delete();

                    // Upload new File
                    if ($request->hasFile('default_file')) {
                        $this->upload_to_s3($task, $request->file('default_file'), $request->custom_name);
                    }

                } else {
                    // Upload new File
                    if ($request->hasFile('default_file')) {
                        $this->upload_to_s3($task, $request->file('default_file'), $request->custom_name);
                    }
                }
            }

            $task->name = $request->name;
            $task->description = $request->description;
            $task->is_history_file_active = $request->is_history_active;
            $task->assign_to = json_encode($request->responsible_person) ?? [];
            $task->save();

            $responsible_person = TaskUser::where('task_id', $task->id)->get();
            
            $responsible_ids = $request->responsible_person ?? [];
            $existing_responsibles = [];
            foreach ($responsible_person as $index => $person) {
                $existing_responsibles[] = $person['user_id'];
            }

            $responsible_diff = array_diff($existing_responsibles, $responsible_ids);

            if (count($responsible_diff)) {
                # delete old
                $responsible_person_will_delete = TaskUser::where('task_id', $task->id);
                $responsible_person_will_delete->delete();

                # save new
                $task->responsible_person()->attach($responsible_ids);
            }

            DB::commit();

            return redirect()->route('tasks.index');

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            if ("The payload is invalid." == $e->getMessage()) {
                return abort(404);
            }
            return abort(500);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($task_id)
    {
        try {
            DB::beginTransaction();
            try {
                $decrypted_id = decrypt($task_id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $task = Task::findOrFail($decrypted_id);

            $files = File::where('task_id', $task->id)->get();

            $list_detele = [];
            foreach ($files as $key => $file) {
                $list_detele[] = $file['path'];
            }
            
            if (count($list_detele)) {
                Storage::disk('s3')->delete($list_detele);
            }

            File::where('task_id', $task->id)->delete();
            $task->delete();
            
            DB::commit();

            return redirect()->route('tasks.index');
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ) {
                return abort(404);
            }

            if ("The payload is invalid." == $e->getMessage()) {
                return abort(404);
            }
            return abort(500);
        }
    }

    public function send_file_task(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'task_id' => 'required|string',
                'file' => 'required|string'
            ]);

        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function upload_to_local($task, $file, $custom_name)
    {
        $filename = $file->getClientOriginalName();
        if ($custom_name) {
            $original_name = $custom_name;
        } else {
            $original_name = pathinfo($filename, PATHINFO_FILENAME);
        }
        
        $mime_type = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();

        // $new_name = $original_name.'_'.time().'.'.$extension;
        // $path = $file->storeAs('public/files', $new_name, 'public');

        $path = Storage::disk('public')->put('files', $file);
        $new_name = basename($path);

        $STATUS_APPROVAL = 0;
        $DEFAULT_FILE = 1;
        $TYPE_FILE = 'internal';

        $file_upload = new File;
        $file_upload->task_id = $task->id;
        $file_upload->status_approve = $STATUS_APPROVAL;
        $file_upload->created_by = Auth::id();
        $file_upload->original_name = $original_name;
        $file_upload->description = 'Default File';
        $file_upload->mime_type = $mime_type;
        $file_upload->new_name = $new_name;
        $file_upload->path = $path;
        $file_upload->is_default = $DEFAULT_FILE;
        $file_upload->type = $TYPE_FILE;
        $file_upload->save();
    }

    public function upload_to_s3($task, $file, $custom_name)
    {
        $filename = $file->getClientOriginalName();
        if ($custom_name) {
            $original_name = $custom_name;
        } else {
            $original_name = pathinfo($filename, PATHINFO_FILENAME);
        }

        $mime_type = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();

        $path = Storage::disk('s3')->put('files', $file);
        $new_name = basename($path);

        $STATUS_APPROVAL = 0;
        $DEFAULT_FILE = 1;
        $TYPE_FILE = 'internal';

        $file_upload = new File;
        $file_upload->task_id = $task->id;
        $file_upload->status_approve = $STATUS_APPROVAL;
        $file_upload->created_by = Auth::id();
        $file_upload->original_name = $original_name;
        $file_upload->description = 'Default File';
        $file_upload->mime_type = $mime_type;
        $file_upload->new_name = $new_name;
        $file_upload->path = $path;
        $file_upload->is_default = $DEFAULT_FILE;
        $file_upload->type = $TYPE_FILE;
        $file_upload->save();
    }
}
