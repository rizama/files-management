@extends('layouts.app')

@section('title')
    Manajemen Tugas - {{ env('APP_NAME') }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
@endsection

@section('page-title')
    Manajemen Tugas
@endsection

@section('child-breadcrumb')
    Ubah Tugas
@endsection

@section('content')
<div class="block block-rounded">
    <div class="block-header">
        <h3 class="block-title">Ubah Tugas</h3>
    </div>
    <div class="block-content block-content-full">
        <form action="{{ route('tasks.update', encrypt($task->id)) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row push">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="name">Nama Tugas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Masukan Nama Tugas" required value="{{old('name', $task->name)}}">
                        @error('name')
                            <span style="color: red">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="name"></label>
                        <div class="custom-control custom-switch mb-1">
                            <input type="checkbox" class="custom-control-input" id="checkbox_history_active" {{ $task->is_history_file_active == 1 ? 'checked' : '' }}>
                            <label class="custom-control-label" for="checkbox_history_active">Aktifkan Riwayat File</label>
                            <input type="hidden" name="is_history_active" id="is_history_active" value="{{old('is_history_active', $task->is_history_file_active)}}">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="description">Deskripsi</label></label>
                        <textarea class="form-control" id="description" name="description"
                            placeholder="Masukan Deskripsi Tugas" >{{old('description', $task->description)}}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>Contoh File</label>
                        <div class="custom-file">
                            <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                            <input type="file" class="custom-file-input" data-toggle="custom-file-input" id="default_file" name="default_file" lang="id">
                            <label class="custom-file-label" for="default_file"></label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="custom_name">Ubah Nama File</label>
                        <input type="text" class="form-control @error('custom_name') is-invalid @enderror" id="custom_name" name="custom_name" placeholder="Masukan Nama File" disabled>
                        @error('custom_name')
                            <span style="color: red">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="responsible_person">Ditugaskan ke</label>
                        <select class="js-select2 form-control" id="example-select2-multiple" name="responsible_person[]" style="width: 100%;" data-placeholder="" multiple>
                            @foreach ($users as $key => $user)
                                <option value="{{ $user->id }}" {{ in_array($user->id, json_decode($task->assign_to)) ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-primary" data-toggle="click-ripple">Simpan</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>jQuery(function () { One.helpers(['select2']);  });</script>
    <script>
        $('#default_file').change(function() {
            if($(this)[0]) $('#custom_name').prop('disabled', false);
        });
        $('#checkbox_history_active').click(function() {
            $('#is_history_active').val(this.checked ? 1 : 0);
        });
    </script>
@endsection