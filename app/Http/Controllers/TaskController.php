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
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            if ($this->user->role->code == 'superadmin') {
                abort(404);
            }
            return $next($request);
        });

        $this->bucket_folder = 'files';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $tasks = Task::with('responsible_person','files')->orderBy('created_at', 'desc')->get();
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
                $q->where('code', '!=', 'superadmin');
            })->get();

            $categories = Category::all();

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
                'assign_to' => 'nullable',
                'default_file' => 'nullable|mimes:'.config('app.accept_file_be'),
                'responsible_person' => 'nullable',
                'custom_name' => 'nullable|string'
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
            $task->is_history_file_active = (int)$request->is_history_active;
            $task->assign_to = $request->responsible_person == null ? json_encode([]) : json_encode($request->responsible_person);
            $task->save();

            $responsible_ids = $request->responsible_person;
            $task->responsible_person()->attach($responsible_ids);

            if ($request->hasFile('default_file')) {
                $this->upload_file_default_to_s3($task, $request->file('default_file'), $request->custom_name, $request->category_id);
            }

            DB::commit();

            $request->session()->flash('task.created', 'Tugas telah dibuat!');
            return redirect()->route('tasks.index');

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
        try {
            $decrypted_id = decrypt($id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        $task = Task::with(['responsible_person','files' => function($q) {
                $q->where('is_default', 0)->with('user', 'status')->orderBy('created_at', 'desc');
            }])
            ->where('id', $decrypted_id)->firstOrFail();
        
        $default_file = Task::with(['files' => function($q) {
            $q->where('is_default', 1)->orderBy('created_at', 'desc')->limit(1);
        }])
        ->findOrFail($decrypted_id);
        
        $files = File::where('status_approve', 3)->orWhere('is_default', 1)->get();

        $ret['task'] = $task;
        $ret['files'] = $files;
        $ret['default_file'] = $default_file->files ? $default_file->files[0] : null;
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
        $task = Task::where('id', $decrypted_id)->firstOrFail();
        $users = User::with(['role'])->whereHas('role', function($q){
            $q->where('code', '!=', 'superadmin');
        })->get();

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
                'category_id' => 'nullable',
                'description' => 'required|string',
                'is_history_active' => 'required',
                'assign_to' => 'nullable|string',
                'default_file' => 'nullable|mimes:'.config('app.accept_file_be'),
                'responsible_person' => 'nullable',
                'custom_name' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $task = Task::with('files')->findOrFail($decrypted_id);

            if ($request->hasFile('default_file')) {
                if (!$request->is_history_active) {

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
                        $this->upload_file_default_to_s3($task, $request->file('default_file'), $request->custom_name, $request->category_id);
                    }

                } else {
                    // Upload new File
                    if ($request->hasFile('default_file')) {
                        $this->upload_file_default_to_s3($task, $request->file('default_file'), $request->custom_name, $request->category_id);
                    }
                }
            }

            $task->name = $request->name;
            $task->category_id = $request->category_id;
            $task->description = $request->description;
            $task->is_history_file_active = $request->is_history_active;
            $task->assign_to = $request->responsible_person == null ? json_encode([]) : json_encode($request->responsible_person);
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

            $request->session()->flash('task.updated', 'Tugas telah diubah!');
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

            $custom_name = $request->custom_name;
            $description = $request->description;
            $category_id = $request->category_id;

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
                    $this->upload_to_s3($task, $request->file('task_file'), $custom_name, $description, $category_id);
                }

            } else {
                // Upload new File
                if ($request->hasFile('task_file')) {
                    $this->upload_to_s3($task, $request->file('task_file'), $custom_name, $description, $category_id);
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

            // Update Files
            $STAUS_APPROVE = 3; // approved

            $file->status_approve = $STAUS_APPROVE; 
            $file->notes = $request->notes; 
            $file->verified_by = Auth::id();
            $file->save();

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
            $STAUS_APPROVE = 4; // rejected

            $file->status_approve = $STAUS_APPROVE; 
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

    public function upload_file_default_to_s3($task, $file, $custom_name, $category_id)
    {
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

    public function upload_to_s3($task, $file, $custom_name, $description, $category_id)
    {
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

        $STATUS_APPROVAL = 2; // waiting aproval
        $DEFAULT_FILE = 0;
        $TYPE_FILE = 'internal';

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
}
