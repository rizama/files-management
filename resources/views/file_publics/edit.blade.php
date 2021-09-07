@extends('layouts.app')

@section('title')
    Manajemen Dokumen Umum - {{ env('APP_NAME') }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('page-title')
    Manajemen Dokumen Umum
@endsection

@section('child-breadcrumb')
    Ubah Dokumen Umum
@endsection

@section('content')
<div class="block block-rounded">
    <div class="block-header">
        <h3 class="block-title">Ubah Dokumen Umum</h3>
    </div>
    <div class="block-content block-content-full">
        <form action="{{ route('file_publics.update', encrypt($file_public->id)) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row push">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Dokumen</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" data-toggle="custom-file-input" id="file" name="file" lang="id" accept="{{ config('app.accept_file_fe') }}">
                            <label class="custom-file-label" for="file"></label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="custom_name">Ubah Nama Dokumen</label>
                        <input type="text" class="form-control @error('custom_name') is-invalid @enderror" id="custom_name" name="custom_name" placeholder="Masukan Nama Dokumen" value="{{old('custom_name', $file_public->original_name)}}">
                        @error('custom_name')
                            <span style="color: red">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="category_id">Kategori</label>
                        <select class="js-select2 form-control" id="select2-categories" name="category_id" data-placeholder="Pilih Kategori Dokumen" data-allow-clear="true" value={{ $file_public->category_id }}>
                            <option></option>
                            @foreach ($categories as $key => $category)
                                <option value="{{ $category->id }}" {{ $category->id == $file_public->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="type">Tipe</label>
                        <select class="js-select2 form-control" id="type" name="type" data-placeholder="Pilih Tipe Dokumen" data-allow-clear="true" disabled>
                            <option value="internal" {{ $file_public->type == 'internal' ? 'selected' : '' }}>Internal</option>
                            <option value="external" {{ $file_public->type == 'external' ? 'selected' : '' }}>External (Untuk Tamu)</option>
                        </select>
                        <input type="hidden" name="type" value="{{ $file_public->type }}">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="description">Deskripsi</label></label>
                        <textarea type="description" class="form-control" id="description" name="description"
                            placeholder="Masukan Deskripsi Dokumen" >{{old('description', $file_public->description)}}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            @include('layouts.edit_submit')
        </form>
    </div>
</div>
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>jQuery(function () { One.helpers(['select2']);  });</script>
    <script>
        $('#file').change(function() {
            if($(this)[0]) $('#custom_name').prop('disabled', false);
        });
    </script>
@endsection