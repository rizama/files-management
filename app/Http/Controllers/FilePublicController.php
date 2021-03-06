<?php

namespace App\Http\Controllers;

use App\Models\FilePublic;
use App\Models\Category;
use App\Models\File;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Auth;

class FilePublicController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            if ($this->user->role->code != 'level_1') {
                abort(403);
            }
            return $next($request);
        })->except('search', 'download_file');

        $this->bucket_folder = config('app.bucket_aws');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $file_publics = FilePublic::with('user')->get();
        $file_internal = File::with('user')->where('task_id', 0)->get();
        $merged = $file_publics->merge($file_internal);

        $ret['file_publics'] = $merged;
        return view('file_publics.index', $ret);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $ret['categories'] = $categories;
        return view('file_publics.create', $ret);
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
            $validator = Validator::make($request->all(), [
                'description' => 'nullable|string',
                'custom_name' => 'nullable|string',
                'category_id' => 'nullable',
                'type' => 'string|in:internal,external',
                'file' => 'required|mimes:'.config('app.accept_file_be')
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $custom_name = $request->custom_name;
            $description = $request->description;
            $category_id = $request->category_id;
            $type = $request->type;
            $file = $request->file;

            $config = [
                "task_id" => 0,
                "file" => $file,
                "custom_name" => $custom_name,
                "category_id" => $category_id,
                "status_approval" => 0,
                "is_default_file" => 1,
                "description" => $description,
                "type" => $type,
            ];
            
            $file_id = $this->upload_file_to_s3($config);
    
            return redirect()->route('file_publics.index');

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
    public function edit(Request $request, $id)
    {
        try {
            $decrypted_id = decrypt($id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        if (!$request->type) {
            abort(404);
        }

        if ($request->type == 'internal') {
            $file_public = File::where('id', $decrypted_id)->firstOrFail();
        } else {
            $file_public = FilePublic::where('id', $decrypted_id)->firstOrFail();
        }

        $categories = Category::all();

        $ret['file_public'] = $file_public;
        $ret['categories'] = $categories;
        return view('file_publics.edit', $ret);
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
            $validator = Validator::make($request->all(), [
                'description' => 'nullable|string',
                'custom_name' => 'nullable|string',
                'category_id' => 'nullable',
                'type' => 'string|in:internal,external',
                'file' => 'nullable|mimes:'.config('app.accept_file_be')
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            try {
                $decrypted_id = decrypt($id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $custom_name = $request->custom_name;
            $description = $request->description;
            $category_id = $request->category_id;
            $type = $request->type;
            $file = $request->file;

            if ($type == 'internal') {
                $old_file = File::findOrFail($decrypted_id);
            } else {
                $old_file = FilePublic::findOrFail($decrypted_id);
            }
            
            if ($request->hasFile('file')) {
                // TODO: Delete Old File
                if ($old_file) {
                    $old_name_file = $old_file->new_name;
                    $exists = Storage::disk('s3')->exists('files/'.$old_name_file);
                    if ($exists) {
                        Storage::disk('s3')->delete('files/'.$old_name_file);
                    }
                    $old_file->delete();
                }
                // Insert new
                if ($request->hasFile('file')) {
                    $config = [
                        "task_id" => 0,
                        "file" => $file,
                        "custom_name" => $custom_name,
                        "category_id" => $category_id,
                        "status_approval" => 0,
                        "is_default_file" => 1,
                        "description" => $description,
                        "type" => $type,
                    ];
                    
                    $this->upload_file_to_s3($config);
                }
            } else {
                $old_file->category_id = $category_id;
                if ($custom_name) {
                    $old_file->original_name = $custom_name;
                }
                $old_file->description = $description;
                $old_file->save();
            }
    
            return redirect()->route('file_publics.index');

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            try {
                $decrypted_id = decrypt($id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            if (!$request->type) {
                abort(404);
            }
    
            if ($request->type == 'internal') {
                $old_file = File::where('id', $decrypted_id)->firstOrFail();
            } else {
                $old_file = FilePublic::where('id', $decrypted_id)->firstOrFail();
            }

            if ($old_file) {
                $old_name_file = $old_file->new_name;
                $exists = Storage::disk('s3')->exists('files/'.$old_name_file);
                if ($exists) {
                    Storage::disk('s3')->delete('files/'.$old_name_file);
                }
            }

            $old_file->delete();
    
            return redirect()->route('file_publics.index');

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

    public function search(Request $request)
    {
        $keyword = $request->q;
        if ($keyword) {

            $files = FilePublic::where('original_name', 'LIKE', "%$keyword%")
                ->orWhere('description', 'LIKE', "%$keyword%")->orderBy('updated_at', 'desc')->get();
            
            $ret['files'] = $files;

            return view('searches.public', $ret);
        }

        $files = FilePublic::orderBy('updated_at', 'desc')->limit(10)->get();

        $ret['files'] = $files;
        return view('searches.public', $ret);
    }

    public function upload_file_to_s3($config)
    {
        $task_id = $config['task_id'];
        $file = $config['file'];
        $custom_name = $config['custom_name'];
        $category_id = $config['category_id'];
        $STATUS_APPROVAL = $config['status_approval'];
        $DEFAULT_FILE = $config['is_default_file'];
        $TYPE_FILE = $config['type'];
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

        if ($TYPE_FILE == 'internal') {
            $file_upload = new File;
            $file_upload->created_by = Auth::id();
            $file_upload->category_id = $category_id;
            $file_upload->task_id = $task_id;
            $file_upload->status_approve = $STATUS_APPROVAL;
            $file_upload->original_name = $original_name;
            $file_upload->description = $description;
            $file_upload->mime_type = $mime_type;
            $file_upload->new_name = $new_name;
            $file_upload->path = $path;
            $file_upload->type = $TYPE_FILE;
            $file_upload->is_default = $DEFAULT_FILE;
            $file_upload->save();
        } else {
            $file_upload = new FilePublic;
            $file_upload->created_by = Auth::id();
            $file_upload->category_id = $category_id;
            $file_upload->original_name = $original_name;
            $file_upload->description = $description;
            $file_upload->mime_type = $mime_type;
            $file_upload->new_name = $new_name;
            $file_upload->path = $path;
            $file_upload->type = $TYPE_FILE;
            $file_upload->save();
        }

        return $file_upload->id;
    }

    public function download_file(Request $request)
    {
        try {
            
            try {
                $decrypted_id = decrypt($request->file);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $type = $request->type;
            
            $id = $decrypted_id;
            $file = FilePublic::findOrFail($id);
            $extension = pathinfo($file->new_name, PATHINFO_EXTENSION);
            $filename = $file->original_name.".".$extension;

            if ($type == 'download') {
                return Storage::disk('s3')->download($file['path'], $filename);
            } else if($type == 'url') {
                return Storage::disk('s3')->url($file['path']);
            } else {
                return redirect()->back();
            }


        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ) {
                return abort(404);
            }
            return abort(500);
        }
    }
}
