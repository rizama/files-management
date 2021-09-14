<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\File;
use App\Models\FilePublic;

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
            return abort(500);
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
            return abort(500);
        }
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
            $type_file = $request->type_file;
            $id = $decrypted_id;

            if ($type_file == 'internal' || $type_file == 'attachment') {
                $file = File::findOrFail($id);
            } else {
                $file = FilePublic::findOrFail($id);
            }

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

    public function destroy(Request $request, $id)
    {
        try {
            try {
                $decrypted_id = decrypt($id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            
            $file = File::findOrFail($decrypted_id);
            $task_id = encrypt($file->task_id);
            $file->delete();

            $request->session()->flash('file.deleted', 'Dokumen telah dihapus!');
            return redirect()->route('tasks.show', $task_id);
        } catch (\Exception $e) {
            return abort(500);
        }
    }
}
