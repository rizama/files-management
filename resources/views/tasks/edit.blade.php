@extends('layouts.app')

@section('title')
    Manajemen Tugas - {{ env('APP_NAME') }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/flatpickr/flatpickr.min.css') }}">
@endsection

@section('page-title')
    Manajemen Tugas
@endsection

@section('child-breadcrumb')
    Ubah Tugas
@endsection

@section('breadcrumb-url')
    {{ url('/tasks') }}
@endsection

@section('content')

<div class="block block-rounded col-lg-6 col-md-8 mx-auto">
    <div class="block-header">
        <h3 class="block-title">Ubah Tugas</h3>
    </div>
    <div class="block-content block-content-full">
        <form action="{{ route('tasks.update', encrypt($task->id)) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row push">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="name">Nama Tugas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Masukan Nama Tugas" required value="{{old('name', $task->name)}}">
                        @error('name')
                            <span style="color: red">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label>Lampiran</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" data-toggle="custom-file-input" id="attachments" name="attachments[]" lang="id" multiple>
                            <label class="custom-file-label" for="attachments"></label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="description">Deskripsi</label></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                            placeholder="Masukan Deskripsi Tugas" >{{old('description', $task->description)}}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="responsible_person">Kategori</label>
                        <select class="js-select2 form-control" id="example-select2" name="category_id" style="width: 100%;" data-placeholder="Pilih Kategori Tugas" data-allow-clear="true" value={{ $task->category_id }}>
                            <option></option>
                            @foreach ($categories as $key => $category)
                                <option value="{{ $category->id }}" {{ $category->id == $task->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <label for="due_date">Batas Waktu Pengerjaan</label>
                        <input type="text" class="js-flatpickr form-control bg-white" id="due_date" name="due_date" data-enable-time="true" data-time_24hr="true" data-min-date="{{ date('Y-m-d').'T'.date('H:i:s') }}" data-default-date={{old('due_date', $task->due_date)}} data-alt-input="true" data-alt-format="j F Y H:i">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input" id="existing_file" name="existing_file" onclick="toggleExistingFile(this)" checked>
                            <label class="custom-control-label" for="existing_file">Gunakan Dokumen yang sudah ada</label>
                        </div>
                    </div>
                </div>
                <div class="container-existing_file col-lg-12">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="responsible_person">Dokumen</label>
                                <select class="js-select2 form-control" id="select2-files" name="file_id" style="width: 100%;" data-placeholder="Pilih Dokumen yang tersedia" data-allow-clear="true">
                                    <option></option>
                                    @foreach ($files as $key => $file)
                                        <option value="{{ $file->id }}" {{ $default_file_id == $file->id ? 'selected' : '' }}>({{ \Carbon\Carbon::parse($file->updated_at)->isoFormat('D MMMM Y, HH:mm') }}) - {{ $file->original_name }}.{{ App\Http\Controllers\TaskController::mime2ext($file->mime_type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container-default_file col-lg-12 d-none">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Contoh Dokumen</label>
                                <div class="custom-file">
                                    <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                                    <input type="file" class="custom-file-input" data-toggle="custom-file-input" id="default_file" name="default_file" lang="id">
                                    <label class="custom-file-label" for="default_file"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
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
                        <label for="responsible_person">Staf Penanggung Jawab
                            <div class="custom-control custom-checkbox ml-2 d-inline">
                                {{-- <input type="checkbox" class="custom-control-input" id="responsible_person_all" name="responsible_person" onclick="toggleAllStaff(this)" {{ $task->assign_to == 'all' ? 'checked' : '' }} value="{{ $task->assign_to == 'all' ? 'all' : '' }}"> --}}
                                <input type="checkbox" class="custom-control-input" id="responsible_person_all" name="responsible_person" onclick="toggleAllStaff(this)">
                                <label class="custom-control-label" for="responsible_person_all">Pilih Semua Staf</label>
                                {{-- @if($task->assign_to == 'all')
                                    <input type="hidden" name="responsible_person" id="responsible_person_all" value="all">
                                @endif --}}
                            </div>
                        </label>
                        <select class="js-select2 form-control responsible_person" id="example-select2-multiple" name="responsible_person[]" style="width: 100%;" data-placeholder="Pilih Staff" multiple {{ $task->assign_to == 'all' ? 'disabled' : '' }}>
                            @foreach ($users as $key => $user)
                                <option value="{{ $user->id }}" {{ is_array(json_decode($task->assign_to)) && in_array($user->id, json_decode($task->assign_to)) ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="form-group">
                        <div class="custom-control custom-switch mb-1">
                            <input type="checkbox" class="custom-control-input" id="checkbox_history_active" {{ $task->is_history_file_active == 1 ? 'checked' : '' }}>
                            <label class="custom-control-label" for="checkbox_history_active">Aktifkan Riwayat Dokumen</label>
                            <input type="hidden" name="is_history_active" id="is_history_active" value="{{old('is_history_active', $task->is_history_file_active)}}">
                        </div>
                        <div class="custom-control custom-switch mb-1">
                            <input type="checkbox" class="custom-control-input" id="checkbox_confirm_all" {{ $task->is_confirm_all == 1 ? 'checked' : '' }}>
                            <label class="custom-control-label" for="checkbox_confirm_all">Konfirmasi Seluruh Dokumen</label>
                            <input type="hidden" name="is_confirm_all" id="is_confirm_all" value="{{old('is_confirm_all', $task->is_confirm_all)}}">
                        </div>
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
    <script src="{{ asset('js/plugins/flatpickr/flatpickr.min.js') }}"></script>

    <script>jQuery(function () { One.helpers(['select2', 'flatpickr']);  });</script>
    <script>
        $('#default_file').change(function() {
            if($(this)[0]) $('#custom_name').prop('disabled', false);
        });
        $('#checkbox_history_active').click(function() {
            $('#is_history_active').val(this.checked ? 1 : 0);
        });
        $('#checkbox_confirm_all').click(function() {
            $('#is_confirm_all').val(this.checked ? 1 : 0);
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

        function toggleAllStaff(e){
            if (e.checked) {
                // $('.responsible_person').prop('disabled', true);
                // $('.responsible_person').val('all');
                // $(e).val('all');
                
                $(".responsible_person > option").prop("selected","selected");
                $(".responsible_person").trigger("change");
            } else {
                $('.responsible_person').val(null).trigger('change');
            //     $('.responsible_person').prop('disabled', false);
            //     $('.responsible_person').val(null);
            //     $('.responsible_person').select2('val', '');
            }
        }
        $('.responsible_person').on('select2:unselect', function (e) {
            $('#responsible_person_all').prop('checked', false);
        });
    </script>
@endsection