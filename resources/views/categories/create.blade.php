@extends('layouts.app')

@section('title')
    Manajemen Kategori - {{ env('APP_NAME') }}
@endsection

@section('page-title')
    Manajemen Kategori
@endsection

@section('child-breadcrumb')
    Tambah Kategori
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title">Tambah Kategori</h3>
        </div>
        <div class="block-content block-content-full">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="row push">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="name">Nama <span class="text-danger">*</span></label></label>
                            <input type="name" class="form-control" id="name" name="name" placeholder="Masukan Nama Kategori"
                                required>
                            @error('name')
                                <span style="color: red">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea type="description" class="form-control" id="description" name="description"
                                placeholder="Masukan Deskripsi Kategori"></textarea>
                            @error('description')
                                <span style="color: red">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                @include('layouts.create_submit')
            </form>
        </div>
    </div>
@endsection
