<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            if ($this->user->role->code != 'superadmin') {
                abort(404);
            }
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
                'name' => 'required|string',
                'description' => 'string'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }

            $category = new Category;
            $category->code = Str::uuid();
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return encrypt($category->id);

            // $request->session()->flash('category.created', 'Kategori Tugas telah dibuat!');
            // return redirect()->route('categories.index');

        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            try {
                $decrypted_id = decrypt($id);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'string'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }
    
            $category = Category::findOrfail($decrypted_id);
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return encrypt($category->id);

            // $request->session()->flash('category.updated', 'Kategori Tugas telah diubah!');
            // return redirect()->route('categories.index');
        } catch (\Exception $e) {
            dd($e);
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ) {
                return abort(404);
            }
            return abort(500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
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

            $category = Category::findOrFail($decrypted_id);
            $category->delete();

            return encrypt($category->id);

            // $request->session()->flash('category.deleted', 'Kategori Tugas telah dihapus!');
            // return redirect()->route('categories.index');

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
}
