<?php

namespace App\Http\Controllers;

use App\Models\FilePublic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Auth;

class FilePublicController extends Controller
{
    public function __construct()
    {
        // $this->middleware(function ($request, $next) {
        //     $this->user = Auth::user();
        //     if ($this->user->role->code == 'superadmin') {
        //         abort(404);
        //     }
        //     return $next($request);
        // });

        $this->bucket_folder = config('app.bucket_aws');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                'file' => 'required|mimes:'.config('app.accept_file_be')
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
                // return redirect()->back()->withErrors($validator->errors());
            }

            $custom_name = $request->custom_name;
            $description = $request->description;
            $category_id = $request->category_id;
            $file = $request->file;

            $file_upload = $this->upload_file_to_s3($file, $custom_name, $description, $category_id);
    
            return $file_upload;
        } catch (\Exception $e) {
            dd($e);
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
        //
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
                'file' => 'nullable|mimes:'.config('app.accept_file_be')
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
                // return redirect()->back()->withErrors($validator->errors());
            }

            try {
                $decrypted_id = decrypt($id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $custom_name = $request->custom_name;
            $description = $request->description;
            $category_id = $request->category_id;
            $file = $request->file;
            $old_file = FilePublic::findOrFail($decrypted_id);
            
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
                    $this->upload_file_to_s3($file, $custom_name, $description, $category_id);
                }
            } else {
                $old_file->category_id = $category_id;
                $old_file->original_name = $custom_name;
                $old_file->description = $description;
                $old_file->save();
            }
    
            return "update success";

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
    public function destroy($id)
    {
        try {
            try {
                $decrypted_id = decrypt($id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $old_file = FilePublic::findOrFail($decrypted_id);
            $old_file->delete();
    
            return "delete success";

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

    public function upload_file_to_s3($file, $custom_name, $description, $category_id)
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

        $TYPE_FILE = 'external';

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

        return $file_upload->id;
    }
}
