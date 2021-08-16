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
    Tambah Tugas
@endsection

@section('content')
<div class="block block-rounded">
    <div class="block-header">
        <h3 class="block-title">Tambah Tugas</h3>
    </div>
    <div class="block-content block-content-full">
        <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row push">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="name">Nama Tugas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Masukan Nama Tugas" required>
                        @error('name')
                            <span style="color: red">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="name"></label>
                        <div class="custom-control custom-switch mb-1">
                            <input type="hidden" name="is_history_active" value="0">
                            <input type="checkbox" class="custom-control-input" id="is_history_active" name="is_history_active">
                            <label class="custom-control-label" for="is_history_active">Aktifkan Riwayat File</label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="responsible_person">Kategori</label>
                        <select class="js-select2 form-control" id="select2-categories" name="category_id" style="width: 100%;" data-placeholder="Pilih Kategori Tugas" data-allow-clear="true">
                            <option></option>
                            @foreach ($categories as $key => $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="email">Deskripsi</label></label>
                        <textarea type="description" class="form-control" id="description" name="description"
                            placeholder="Masukan Deskripsi Tugas" ></textarea>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input" id="existing_file" name="existing_file" onclick="toggleExistingFile(this)">
                            <label class="custom-control-label" for="existing_file">Gunakan Dokumen yang sudah ada</label>
                        </div>
                    </div>
                </div>
                <div class="container-existing_file d-none col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="responsible_person">Dokumen</label>
                                <select class="js-select2 form-control" id="select2-files" name="file_id" style="width: 100%;" data-placeholder="Pilih Dokumen yang tersedia" data-allow-clear="true">
                                    <option></option>
                                    @foreach ($files as $key => $file)
                                        <option value="{{ $file->id }}">({{ \Carbon\Carbon::parse($file->updated_at)->isoFormat('D MMMM Y, HH:mm') }}) - {{ $file->original_name }}.{{ App\Http\Controllers\TaskController::mime2ext($file->mime_type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container-default_file col-lg-12">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Contoh Dokumen</label>
                                <div class="custom-file">
                                    <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                                    <input type="file" class="custom-file-input" data-toggle="custom-file-input" id="default_file" name="default_file" lang="id" accept="{{ config('app.accept_file_fe') }}">
                                    <label class="custom-file-label" for="default_file"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="custom_name">Ubah Nama Dokumen</label>
                                <input type="text" class="form-control @error('custom_name') is-invalid @enderror" id="custom_name" name="custom_name" placeholder="Masukan Nama Dokumen" disabled>
                                @error('custom_name')
                                    <span style="color: red">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="responsible_person">Petugas</label>
                        <select class="js-select2 form-control" id="example-select2-multiple" name="responsible_person[]" style="width: 100%;" data-placeholder="" multiple>
                            @foreach ($users as $key => $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @include('layouts.create_submit')
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
        $('#is_history_active').click(function() {
            $(this).val(this.checked ? 1 : 0);
        });

        function toggleExistingFile(e){
            if (e.checked) {
                $('.container-default_file').addClass('d-none');
                $('.container-existing_file').removeClass('d-none');
                $('#default_file').val(null);
                $('#custom_name').val(null);
            } else {
                $('.container-default_file').removeClass('d-none');
                $('.container-existing_file').addClass('d-none');
                $('#select2-files').val(null);
            }
        }
    </script>
@endsection