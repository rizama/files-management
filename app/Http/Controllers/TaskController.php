<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\Facades\Validator;

use App\Models\Task;
use App\Models\Category;
use App\Models\File;
use App\Models\TaskUser;
use Auth;
use Illuminate\Support\Facades\DB;
use App\User;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            if ($this->user->role->code == 'superadmin' || $this->user->role->code == 'guest') {
                abort(403);
            }
            return $next($request);
        });

        $this->bucket_folder = config('app.bucket_aws');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $tasks = Task::with(['responsible_person', 'category', 'files' => function($q){
                $q->where('is_default', 0)->where('type', 'internal');
            }])->orderBy('created_at', 'desc')->get();
            $ret['tasks'] = $tasks;
            $ret['user'] = Auth::user();

            return view('tasks.index', $ret);
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $users = User::with(['role'])->whereHas('role', function($q){
                $q->where('code', '!=', 'superadmin')
                ->where('code', '!=', 'guest')
                ->where('code', '!=', 'level_1');
            })->get();

            $categories = Category::all();

            $files = File::where('status_approve', 3)->orWhere('is_default', 1)->orderBy('updated_at', 'desc')->get();

            $ret['files'] = $files;
            $ret['users'] = $users;
            $ret['categories'] = $categories;
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
                'category_id' => 'nullable',
                'description' => 'nullable|string',
                'is_history_active' => 'required',
                'default_file' => 'nullable|mimes:'.config('app.accept_file_be'),
                'responsible_person' => 'nullable',
                'custom_name' => 'nullable|string',
                'due_date' => 'nullable',
                'is_confirm_all' => 'nullable',
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $ON_PROGRESS = 1;

            $task = new Task;
            $task->created_by = Auth::id();
            $task->name = $request->name;
            $task->category_id = $request->category_id;
            $task->description = $request->description ?? '';
            $task->status = $ON_PROGRESS;
            $task->due_date = $request->due_date;
            $task->is_confirm_all = $request->is_confirm_all;
            $task->is_history_file_active = (int)$request->is_history_active;
            if ($request->responsible_person) {
                $task->assign_to = is_array($request->responsible_person) ? json_encode($request->responsible_person) : $request->responsible_person;
            }
            $task->save();

            if ($request->responsible_person) {
                if (is_array($request->responsible_person)) {
                    $responsible_ids = $request->responsible_person;
                    $task->responsible_person()->attach($responsible_ids);
                }
            }

            if ($request->hasFile('default_file')) {
                $config = [
                    "task" => $task,
                    "file" => $request->file('default_file'),
                    "custom_name" => $request->custom_name,
                    "category_id" => $request->category_id,
                    "status_approval" => 0,
                    "is_default_file" => 1,
                    "type_file" => 'internal',
                    "description" => 'Default File',
                ];

                $file_id = $this->upload_file_to_s3($config);
            } else {
                $file_id = null;
            }

            if ($request->attachments) {
                foreach ($request->attachments as $attachement) {
                    $config = [
                        "task" => $task,
                        "file" => $attachement,
                        "custom_name" => null,
                        "category_id" => $request->category_id,
                        "status_approval" => 0,
                        "is_default_file" => 0,
                        "type_file" => 'attachment',
                        "description" => 'Attachment',
                    ];

                    $this->upload_file_to_s3($config);
                }
            }

            if ($request->existing_file == "on") {
                $task->default_file_id = $request->file_id;
            } else {
                $task->default_file_id = $file_id;
            }
            $task->save();

            DB::commit();

            $request->session()->flash('task.created', 'Tugas telah dibuat!');
            return redirect()->route('tasks.index');

        } catch (\Exception $e) {
            DB::rollback();
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
        try {
            $decrypted_id = decrypt($id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        $task = Task::with(['status_task', 'user', 'validator','responsible_person','files' => function($q) {
                $q->where('is_default', 0)->where('type', 'internal')->with('user', 'status')->orderBy('created_at', 'desc');
            }, 'default_file'])
            ->where('id', $decrypted_id)->firstOrFail();

        $attachements = File::where('task_id', $decrypted_id)->where('is_default', 0)->where('type', 'attachment')->get();

        $default_file = Task::with(['files' => function($q) {
            $q->where('is_default', 1)->orderBy('created_at', 'desc')->limit(1);
        }])
        ->findOrFail($decrypted_id);

        foreach ($task->files as $key => $file) {
            $file['file_url'] = $this->generate_url($file->id);
        }

        if (count($default_file->files) > 0) {
            if ($task->default_file) {
                $default = $task->default_file;
            } else {
                $default = $default_file->files[0];
            }
        } else {
            if ($task->default_file_id) {
                $file_default = File::where('id', $task->default_file_id)->first();
                $default = $file_default;
            } else {
                $default = null;
            }
            
        }
        $ret['task'] = $task;
        $ret['attachements'] = $attachements;
        $ret['default_file'] = $default;
        // dd($ret);
        return view('tasks.show', $ret);
        
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
        $task = Task::with(['default_file', 'files' => function($q) {
            $q->where('is_default', 1)->with('user', 'status')->orderBy('created_at', 'desc');
        }])->where('id', $decrypted_id)->firstOrFail();
        $users = User::with(['role'])->whereHas('role', function($q){
            $q->where('code', '!=', 'superadmin')
            ->where('code', '!=', 'guest')
            ->where('code', '!=', 'level_1');
        })->get();
        $categories = Category::all();

        $files = File::where('status_approve', 3)->orWhere('is_default', 1)->orderBy('updated_at', 'desc')->get();

        if ($task->default_file) {
            $default_file_id = $task->default_file->id;
        } else {
            if ($task->default_file_id) {
                $default_file_id = $task->files[0]->id;
            } else {
                $default_file_id = null;
            }
        }

        $ret['files'] = $files;
        $ret['users'] = $users;
        $ret['task'] = $task;
        $ret['categories'] = $categories;
        $ret['default_file_id'] = $default_file_id;
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
            // dd($data);
            try {
                $decrypted_id = decrypt($id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'category_id' => 'nullable',
                'description' => 'nullable|string',
                'is_history_active' => 'required',
                'default_file' => 'nullable|mimes:'.config('app.accept_file_be'),
                'responsible_person' => 'nullable',
                'custom_name' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $task = Task::with('files')->findOrFail($decrypted_id);
            $file_attach = File::where('type', 'attachment')->where('task_id', $task->id)->get();

            if ($request->attachments) {
                if (count($file_attach)) {
                    // Delete first existing attachment from S3 and Database
                    foreach ($file_attach as $attach) {
                        $old_name_file = $attach->new_name;
                        $exists = Storage::disk('s3')->exists('files/'.$old_name_file);
                        if ($exists) {
                            Storage::disk('s3')->delete('files/'.$old_name_file);
                        }
                    }
                    $file_attach->each->delete();
                }

                // Insert new attachment
                foreach ($request->attachments as $attachement) {
                    $config = [
                        "task" => $task,
                        "file" => $attachement,
                        "custom_name" => null,
                        "category_id" => $request->category_id,
                        "status_approval" => 0,
                        "is_default_file" => 0,
                        "type_file" => 'attachment',
                        "description" => 'Attachment',
                    ];

                    $this->upload_file_to_s3($config);
                }
            }

            if ($request->hasFile('default_file')) {
                $config = [
                    "task" => $task,
                    "file" => $request->file('default_file'),
                    "custom_name" => $request->custom_name,
                    "category_id" => $request->category_id,
                    "status_approval" => 0,
                    "is_default_file" => 1,
                    "type_file" => 'internal',
                    "description" => 'Default File',
                ];

                if (!$request->is_history_active) {
                    
                    // Remove Selected 
                    $task->default_file_id = null;
                    
                    // Delete Old File
                    $old_file = $task->files->last();
                    if ($old_file) {
                        $old_name_file = $old_file->new_name;
                        $exists = Storage::disk('s3')->exists('files/'.$old_name_file);
                        if ($exists) {
                            Storage::disk('s3')->delete('files/'.$old_name_file);
                        }
                        $old_file->delete();
                    }

                    // Upload new File
                    if ($request->hasFile('default_file')) {        
                        $file_id = $this->upload_file_to_s3($config);
                    }

                } else {
                    // Upload new File
                    if ($request->hasFile('default_file')) {
                        $file_id = $this->upload_file_to_s3($config);
                    }
                }

                $task->default_file_id = $file_id;
            }

            $task->name = $request->name;
            $task->category_id = $request->category_id;

            if ($request->file_id) {
                $task->default_file_id = $request->file_id;
            }
            
            $task->description = $request->description;
            $task->due_date = $request->due_date;
            $task->is_confirm_all = $request->is_confirm_all ? $request->is_confirm_all : 0;
            $task->is_history_file_active = $request->is_history_active;
            $task->assign_to = is_array($request->responsible_person) ? json_encode($request->responsible_person) : $request->responsible_person;
            $task->save();

            $responsible_person = TaskUser::where('task_id', $task->id)->get();

            if ($request->responsible_person) {
                if (is_array($request->responsible_person)) {
                    $responsible_ids = count($request->responsible_person) ? $request->responsible_person : [];

                    if (count($responsible_person)) {
                        $existing_responsibles = [];
                        foreach ($responsible_person as $index => $person) {
                            $existing_responsibles[] = $person['user_id'];
                        }

                        $responsible_diff = array_diff($responsible_ids, $existing_responsibles);
                        if (!($responsible_ids == $existing_responsibles)) {
                            # delete old
                            $responsible_person_will_delete = TaskUser::where('task_id', $task->id);
                            $responsible_person_will_delete->delete();

                            # save new
                            $task->responsible_person()->attach($responsible_ids);
                        }
                    } else {
                        $task->responsible_person()->attach($responsible_ids);
                    }
                } else {
                    $responsible_person_will_delete = TaskUser::where('task_id', $task->id);
                    $responsible_person_will_delete->delete();
                }
            } else {
                $responsible_person_will_delete = TaskUser::where('task_id', $task->id);
                $responsible_person_will_delete->delete();
            }

            DB::commit();

            $request->session()->flash('task.updated', 'Tugas telah diubah!');
            return redirect()->route('tasks.index');

        } catch (\Exception $e) {
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
            $task->responsible_person()->detach();

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
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ) {
                return abort(404);
            }

            if ("The payload is invalid." == $e->getMessage()) {
                return abort(404);
            }
            return abort(500);
        }
    }

    public function send_file_task(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'custom_name' => 'nullable|string',
                'description' => 'nullable|string',
                'task_file' => 'required|mimes:'.config('app.accept_file_be'),
                'category_id' => 'nullable'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            try {
                $task_id = decrypt($id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $task = Task::where('id', $task_id)->firstOrFail();

            if (count($task->responsible_person)) {
                $ids = [];
                foreach ($task->responsible_person as $a => $responsible) {
                    array_push($ids, $responsible->id);
                }

                if (!in_array(Auth::id(), $ids)) {
                    return redirect()->back()->withErrors([
                        "message" => "not authorized"
                    ]);
                }
            }

            $custom_name = $request->custom_name;
            $description = $request->description;
            $category_id = $request->category_id;

            $config = [
                "task" => $task,
                "file" => $request->file('task_file'),
                "custom_name" => $custom_name,
                "category_id" => $category_id,
                "status_approval" => 2,
                "is_default_file" => 0,
                "type_file" => 'internal',
                "description" => $description,
            ];

            if (!$task->is_history_file_active) {
                // Delete Old File
                $old_file = $task->files->where('is_default', '!=', 1)->last();
                if ($old_file) {
                    $old_name_file = $old_file->new_name;
                    $exists = Storage::disk('s3')->exists('files/'.$old_name_file);
                    if ($exists) {
                        Storage::disk('s3')->delete('files/'.$old_name_file);
                    }
                    $old_file->delete();
                }

                // Upload new File
                if ($request->hasFile('task_file')) {
                    $this->upload_file_to_s3($config);
                }

            } else {
                // Upload new File
                if ($request->hasFile('task_file')) {
                    $this->upload_file_to_s3($config);
                }
            }

            $request->session()->flash('task.file_uploaded', 'Dokumen berhasil diunggah!');
            return redirect()->route('tasks.show', encrypt($task->id));

        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ) {
                return abort(404);
            }

            if ("The payload is invalid." == $e->getMessage()) {
                return abort(404);
            }
            return abort(500);
        }
    }

    public function send_note_task(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'note_name' => 'required|string',
                'note_content' => 'required|string',
                'category_id' => 'nullable'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            try {
                $task_id = decrypt($id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $task = Task::where('id', $task_id)->firstOrFail();

            if (count($task->responsible_person)) {
                $ids = [];
                foreach ($task->responsible_person as $a => $responsible) {
                    array_push($ids, $responsible->id);
                }

                if (!in_array(Auth::id(), $ids)) {
                    return redirect()->back()->withErrors([
                        "message" => "not authorized"
                    ]);
                }
            }

            if (!$task->is_history_file_active) {
                // Delete Old File
                $old_file = $task->files->where('is_default', '!=', 1)->last();
                if ($old_file) {
                    $old_name_file = $old_file->new_name;
                    $exists = Storage::disk('s3')->exists('files/'.$old_name_file);
                    if ($exists) {
                        Storage::disk('s3')->delete('files/'.$old_name_file);
                    }
                    $old_file->delete();
                }

                // Upload new File
                $this->upload_note_to_s3($task, $request->note_name, $request->note_content, $request->category_id);

            } else {
                // Upload new File
                $this->upload_note_to_s3($task, $request->note_name, $request->note_content, $request->category_id);
            }

            $request->session()->flash('task.file_uploaded', 'Dokumen berhasil diunggah!');
            return redirect()->route('tasks.show', encrypt($task->id));
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ) {
                return abort(404);
            }
            if ("The payload is invalid." == $e->getMessage()) {
                return abort(404);
            }
            return abort(500);
        }
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $decrypted_id = decrypt($id);

            $validator = Validator::make($request->all(), [
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $file = File::with('task')->findOrFail($decrypted_id);

            $STATUS_APPROVE = 3; // approved

            if ($file->task->is_confirm_all == 0) {
                $task = Task::with('files')->where('id', $file->task->id)->firstorFail();
                foreach ($task->files as $key => $file_to_approve) {
                    $file_to_approve->status_approve = $STATUS_APPROVE;
                    $file_to_approve->notes = $request->notes; 
                    $file_to_approve->verified_by = Auth::id();
                    $file_to_approve->save();
                }
            } else {
                // Update Files
                $file->status_approve = $STATUS_APPROVE; 
                $file->notes = $request->notes; 
                $file->verified_by = Auth::id();
                $file->save();
            }

            DB::commit();
            $request->session()->flash('file.approved', 'Dokumen disetujui!');
            return redirect()->route('tasks.show', encrypt($file->task_id));

        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $decrypted_id = decrypt($id);

            $validator = Validator::make($request->all(), [
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $file = File::with('task')->findOrFail($decrypted_id);

            // Update Files
            $STATUS_APPROVE = 4; // rejected

            $file->status_approve = $STATUS_APPROVE; 
            $file->notes = $request->notes; 
            $file->verified_by = Auth::id();
            $file->save();

            DB::commit();
            $request->session()->flash('file.reject', 'Dokumen ditolak!');
            return redirect()->route('tasks.show', encrypt($file->task_id));

        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function approve_task(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $decrypted_id = decrypt($id);

            $task = Task::with(['files' => function($q){
                $q->orderBy('created_at', 'desc');
            }])->findOrFail($decrypted_id);

            $STATUS_APPROVE = 3; // approved

            if (count($task->files)) {
                foreach ($task->files as $key => $file_to_approve) {
                    $file_to_approve->status_approve = $STATUS_APPROVE;
                    $file_to_approve->verified_by = Auth::id();
                    $file_to_approve->save();
                }
            }

            $task->status = $STATUS_APPROVE; 
            $task->verified_by = Auth::id();
            $task->save();

            DB::commit();

            $request->session()->flash('task.approved', 'Tugas selesai!');
            return redirect()->route('tasks.show', encrypt($task->id));

        } catch (\Exception $e) {
            DB::rollback();
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ) {
                return abort(404);
            }
            if ("The payload is invalid." == $e->getMessage()) {
                return abort(404);
            }
            return abort(500);
        }
    }

    public function my_task(Request $request)
    {
        if (Auth::user()->role->code == 'level_1') {
            abort(403);
        }

        try {
            $user_id = Auth::id(); 
            $user = User::with(['responsible_tasks.category', 'responsible_tasks.status_task', 'responsible_tasks.files' => function($q) use($user_id){
                $q->where('is_default', 0)->where('type', 'internal')->where('created_by', $user_id);
            }])->where('id', $user_id)->first();

            $tasks_general = Task::with(['files' => function($q) use($user_id){
                $q->where('is_default', 0)->where('type', 'internal')->where('created_by', $user_id);
            }])->where('assign_to', 'all')->get();
            // dd($tasks_general);
            
            foreach ($tasks_general as $key => $general) {
                if ($user->role->code != 'level_1') {
                    $user->responsible_tasks[] = $general;
                }
            }

            $task_total = count($user->responsible_tasks);
            $task_finished = 0;
            $task_progress = 0;
            $task_unprogress = 0;
            foreach ($user->responsible_tasks as $key => $task) {
                if ($task->status == 3) {
                    $task_finished++;
                } else {
                    if (count($task->files)) {
                        $task_progress++;
                    } else {
                        $task_unprogress++;
                    }
                }
            }

            $doc_total = 0;
            $doc_finished = 0;
            $doc_progress = 0;
            $doc_reject = 0;
            foreach ($user->responsible_tasks as $key => $task) {
                $doc_total = $doc_total + count($task->files);
                $doc_finished = $doc_finished + count($task->files->where('status_approve', 3));
                $doc_progress = $doc_progress + count($task->files->where('status_approve', 2));
                $doc_reject = $doc_reject + count($task->files->where('status_approve', 4));
            }
            
            $ordered = $user->responsible_tasks->sortByDesc('created_at');
            $user->responsible_tasks = $ordered;
            
            $ret['task_finished'] = $task_finished;
            $ret['task_progress'] = $task_progress;
            $ret['task_unprogress'] = $task_unprogress;
            $ret['task_total'] = $task_total;

            $ret['doc_total'] = $doc_total;
            $ret['doc_finished'] = $doc_finished;
            $ret['doc_progress'] = $doc_progress;
            $ret['doc_reject'] = $doc_reject;
            
            $ret['user'] = $user;

            return view('mytasks.index', $ret);

        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ) {
                return abort(404);
            }
            if ("The payload is invalid." == $e->getMessage()) {
                return abort(404);
            }
            return abort(500);
        }
    }

    public function upload_to_local($task, $file, $custom_name, $category_id)
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
        $file_upload->category_id = $category_id;
        $file_upload->original_name = $original_name;
        $file_upload->description = 'Default File';
        $file_upload->mime_type = $mime_type;
        $file_upload->new_name = $new_name;
        $file_upload->path = $path;
        $file_upload->is_default = $DEFAULT_FILE;
        $file_upload->type = $TYPE_FILE;
        $file_upload->save();
    }

    public function upload_file_to_s3($config)
    {
        $task = $config['task'];
        $file = $config['file'];
        $custom_name = $config['custom_name'];
        $category_id = $config['category_id'];
        $STATUS_APPROVAL = $config['status_approval'];
        $DEFAULT_FILE = $config['is_default_file'];
        $TYPE_FILE = $config['type_file'];
        $description = $config['description'];

        $filename = $file->getClientOriginalName();
        if ($custom_name) {
            $original_name = $custom_name;
        } else {
            $original_name = pathinfo($filename, PATHINFO_FILENAME);
        }

        $mime_type = $file->getMimeType();
        $extension = $file->getClientOriginalExtension();

        $path = Storage::disk('s3')->put($this->bucket_folder, $file);
        $new_name = basename($path);

        $file_upload = new File;
        $file_upload->task_id = $task->id;
        $file_upload->status_approve = $STATUS_APPROVAL;
        $file_upload->created_by = Auth::id();
        $file_upload->category_id = $category_id;
        $file_upload->original_name = $original_name;
        $file_upload->description = $description;
        $file_upload->mime_type = $mime_type;
        $file_upload->new_name = $new_name;
        $file_upload->path = $path;
        $file_upload->is_default = $DEFAULT_FILE;
        $file_upload->type = $TYPE_FILE;
        $file_upload->save();

        return $file_upload->id;
    }

    public function upload_note_to_s3($task, $note_name, $note_content, $category_id)
    {
        $filename = $note_name;

        $mime_type = 'text/plain';
        $extension = '.txt';
        $fullname = time().$extension;

        Storage::disk('s3')->put($this->bucket_folder."/".$fullname, $note_content);

        $STATUS_APPROVAL = 2; // waiting aproval
        $DEFAULT_FILE = 0;
        $TYPE_FILE = 'internal';

        $file_upload = new File;
        $file_upload->task_id = $task->id;
        $file_upload->status_approve = $STATUS_APPROVAL;
        $file_upload->created_by = Auth::id();
        $file_upload->category_id = $category_id;
        $file_upload->original_name = $filename;
        $file_upload->description = $note_content;
        $file_upload->mime_type = $mime_type;
        $file_upload->new_name = $fullname;
        $file_upload->path = $this->bucket_folder."/".$fullname;
        $file_upload->is_default = $DEFAULT_FILE;
        $file_upload->type = $TYPE_FILE;
        $file_upload->save();

        return $file_upload->id;
    }

    static public function mime2ext($mime) 
    {
        $mime_map = [
            'image/bmp'                                                                 => 'bmp',
            'image/x-bmp'                                                               => 'bmp',
            'image/x-bitmap'                                                            => 'bmp',
            'image/x-xbitmap'                                                           => 'bmp',
            'image/x-win-bitmap'                                                        => 'bmp',
            'image/x-windows-bmp'                                                       => 'bmp',
            'image/ms-bmp'                                                              => 'bmp',
            'image/x-ms-bmp'                                                            => 'bmp',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
            'image/jpeg'                                                                => 'jpeg',
            'image/pjpeg'                                                               => 'jpeg',
            'application/pdf'                                                           => 'pdf',
            'application/octet-stream'                                                  => 'pdf',
            'image/png'                                                                 => 'png',
            'image/x-png'                                                               => 'png',
            'application/powerpoint'                                                    => 'ppt',
            'application/vnd.ms-powerpoint'                                             => 'ppt',
            'application/vnd.ms-office'                                                 => 'ppt',
            'application/msword'                                                        => 'doc',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/x-rar'                                                         => 'rar',
            'application/rar'                                                           => 'rar',
            'application/x-rar-compressed'                                              => 'rar',
            'text/rtf'                                                                  => 'rtf',
            'text/richtext'                                                             => 'rtx',
            'text/plain'                                                                => 'txt',
            'application/excel'                                                         => 'xl',
            'application/msexcel'                                                       => 'xls',
            'application/x-msexcel'                                                     => 'xls',
            'application/x-ms-excel'                                                    => 'xls',
            'application/x-excel'                                                       => 'xls',
            'application/x-dos_ms_excel'                                                => 'xls',
            'application/xls'                                                           => 'xls',
            'application/x-xls'                                                         => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
            'application/vnd.ms-excel'                                                  => 'xlsx',
            'application/x-zip'                                                         => 'zip',
            'application/zip'                                                           => 'zip',
            'application/x-zip-compressed'                                              => 'zip',
            'application/s-compressed'                                                  => 'zip',
            'multipart/x-zip'                                                           => 'zip',
        ];

        return isset($mime_map[$mime]) ? $mime_map[$mime] : false;
    }

    public function generate_url($id)
    {
        try {
            $file = File::findOrFail($id);
            $extension = pathinfo($file->new_name, PATHINFO_EXTENSION);
            return Storage::disk('s3')->url($file['path']);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ) {
                return abort(404);
            }
            return abort(500);
        }
    }
}
