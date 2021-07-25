<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\File;

class FileController extends Controller
{
    public function get_local_file(Request $request)
    {
        try {

            $type = $request->type;
            
            $id = 11;
            $file = File::findOrFail($id);

            if ($type == 'download') {
                return Storage::disk('public')->download($file['path']);
            } else if($type == 'url') {
                return Storage::disk('public')->url($file['path']);
            } else {
                dd("Not Found Type");
            }


        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ) {
                return abort(404);
            }
            dd($e);
        }
    }

    public function get_s3_file(Request $request)
    {
        try {

            $type = $request->type;
            
            $id = 12;
            $file = File::findOrFail($id);

            if ($type == 'download') {
                return Storage::disk('s3')->download($file['path']);
            } else if($type == 'url') {
                return Storage::disk('s3')->url($file['path']);
            } else {
                dd("Not Found Type");
            }


        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ) {
                return abort(404);
            }
            dd($e);
        }
    }
}
