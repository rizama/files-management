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
    {{ $task->name }}
@endsection

@section('info-page-title')
    {{ $task->description }} 
@endsection

@section('content')
@if (session()->has('task.file_uploaded'))
<div class="alert alert-success alert-dismissable" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <p class="mb-0">{{ session('task.file_uploaded') }}</p>
</div>
@endif

@if (session()->has('file.approved'))
<div class="alert alert-success alert-dismissable" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <p class="mb-0">{{ session('file.approved') }}</p>
</div>
@endif

@if (session()->has('file.reject'))
<div class="alert alert-warning alert-dismissable" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <p class="mb-0">{{ session('file.reject') }}</p>
</div>
@endif

<div class="row push">
    <div class="col-lg-8">
        @if($task->status !== 3)
            <div class="block block-rounded">
                <ul class="nav nav-tabs nav-tabs-block align-items-center" data-toggle="tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#tab-file">Unggah Dokumen</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tab-note">Unggah Catatan</a>
                    </li>
                    <li class="nav-item ml-auto">
                        <div class="block-options pl-3 pr-2">
                            <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
                        </div>
                    </li>
                </ul>
                <div class="block-content tab-content">
                    <div class="tab-pane active" id="tab-file" role="tabpanel">
                        <div class="block-header">
                            <h3 class="block-title">Unggah Dokumen</h3>
                        </div>
                        <form action="{{ route('tasks.send_file', encrypt($task->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="category_id" value="{{$task->category_id}}">
                            <div class="block-content block-content-full">
                                <div class="col-lg-12">
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">File</label>
                                        <div class="custom-file col-lg-9">
                                            <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                                            <input type="file" class="custom-file-input @error('task_file') is-invalid @enderror" data-toggle="custom-file-input" id="task_file" name="task_file" lang="id" accept="{{ config('app.accept_file_fe') }}">
                                            <label class="custom-file-label" for="task_file"></label>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="custom_name" class="col-lg-3 col-form-label">Ubah Nama File</label>
                                        <input type="text" class="form-control col-lg-9 @error('custom_name') is-invalid @enderror" id="custom_name" name="custom_name" placeholder="Masukan Nama File" disabled>
                                        @error('custom_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group row">
                                        <label for="description" class="col-lg-3 col-form-label">Deskripsi</label>
                                        <textarea name="description" id="description" class="form-control col-lg-9 @error('description') is-invalid @enderror" placeholder="Masukan Deskripsi" disabled></textarea>
                                        @error('description')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                @error('task_file')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <div class="row">
                                    <div class="col-lg-12 text-right">
                                        <button type="submit" class="btn btn-primary btn-submit btn-file" data-toggle="click-ripple" disabled>Unggah</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="tab-note" role="tabpanel">
                        <div class="block-header">
                            <h3 class="block-title">Unggah Catatan</h3>
                        </div>
                        <form action="{{ route('tasks.send_note', encrypt($task->id)) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="category_id" value="{{$task->category_id}}">
                            <div class="block-content block-content-full">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="note_name" class="col-form-label">Nama Catatan</label>
                                        <input type="text" class="form-control @error('note_name') is-invalid @enderror" id="note_name" name="note_name" placeholder="Masukan Nama File">
                                        @error('note_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="note_content" class="col-form-label">Isi Catatan</label>
                                        <textarea name="note_content" id="note_content" class="form-control @error('note_content') is-invalid @enderror" placeholder="Masukan Deskripsi"></textarea>
                                        @error('note_content')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-right">
                                        <button type="submit" class="btn btn-primary btn-submit" data-toggle="click-ripple" id="btn-note" disabled>Unggah</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        <div class="block block-rounded">
            <div class="block-header">
                <h3 class="block-title">Riwayat File</h3>
            </div>
            <div class="block-content block-content-full">
                <ul class="timeline timeline-alt py-0 timeline-file">
                    @forelse ($task->files as $key => $file)
                    <li class="timeline-event">
                        <div class="timeline-event-icon bg-default">
                            <i class="fa fa-file-alt"></i>
                        </div>
                        <div class="timeline-event-block block invisible" data-toggle="appear">
                            <div class="block-header">
                                <h3 class="block-title"><small>Pengunggah </small>{{ $file->user['name']}}</h3>
                                <div class="block-options">
                                    <div class="timeline-event-time block-options-item font-size-sm">
                                        {{ \Carbon\Carbon::parse($file['created_at'])->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            <div class="block-content pt-0">
                                <p class="block-title">
                                    <small>Deskripsi</small>{{ $file['description'] ?? '-' }}
                                </p>
                                <a href="{{ route('download') }}?file={{ encrypt($file->id) }}&type=download" target="_blank" class="btn btn-secondary mb-2" title="{{$file->original_name}}.{{ App\Http\Controllers\TaskController::mime2ext($file->mime_type) }}">Unduh File</a>
                                @if(in_array(App\Http\Controllers\TaskController::mime2ext($file->mime_type), ['png', 'jpeg', 'jpg', 'pdf', 'bmp']))
                                    <button type="button" class="btn btn-alt-primary push mb-2" data-toggle="modal" data-target="#preview-modal" data-file="{{$file}}" id="preview-btn-modal">Pratinjau Dokumen</button>
                                @endif

                                @if ($file->status['code'] == 'waiting' && $task->status !== 3)
                                    @if (Auth::user()->role->code == 'level_1')
                                    <div class="accordion mt-2" id="accordionExample">
                                        <div class="card">
                                            <div class="card-header p-0" id="headingOne">
                                                <h2 class="mb-0">
                                                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse-{{ $key }}" aria-expanded="true" aria-controls="collapse-{{ $key }}">
                                                        Konfirmasi Dokumen
                                                    </button>
                                                </h2>
                                            </div>
                                            <form action="" method="POST" id="verification">
                                                @csrf
                                                <div id="collapse-{{ $key }}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                    <div class="card-body">
                                                        <div class="row push">
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    <label for="notes">Catatan</label>
                                                                    <input type="text" class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" placeholder="Tambahkan Catatan (Opsional)" required>
                                                                    @error('notes')
                                                                        <span style="color: red">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12 text-right">
                                                                <a
                                                                    class="btn btn-success approve-file js-swal-confirm-with-form"
                                                                    data-type_button="approve"
                                                                    href="{{ route('tasks.approve', encrypt($file->id)) }}"
                                                                    title="Apakah anda yakin untuk menyetujui file ini ?"
                                                                    data-caption="{{$file->original_name ?? ''}}.{{ App\Http\Controllers\TaskController::mime2ext($file->mime_type) }} {{$file->description ? ', Deskripsi: '.$file->description : ''}}"
                                                                    data-form_id="verification"
                                                                    data-success_text="File Berhasil Disetujui"
                                                                >Setujui</a>
                                                                <a
                                                                    class="btn btn-danger reject-file js-swal-confirm-with-form"
                                                                    data-type_button="reject"
                                                                    href="{{ route('tasks.reject', encrypt($file->id)) }}"
                                                                    title="Apakah anda yakin untuk menolak file ini ?"
                                                                    data-caption="{{$file->original_name ?? ''}}.{{ App\Http\Controllers\TaskController::mime2ext($file->mime_type) }}{{$file->description ? ', Deskripsi: '.$file->description : ''}}"
                                                                    data-form_id="verification"
                                                                    data-success_text="File Berhasil Ditolak"
                                                                >Tolak</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <form>
                                        </div>
                                    </div>
                                    @else
                                    <div class="mt-2">
                                        <div>
                                            <span class="badge badge-info">Sedang Menunggu Konfirmasi</span>
                                        </div>
                                    </div>
                                    @endif
                                @else 
                                <div class="mt-2">
                                    @php
                                        if($file->status['code'] == 'waiting'){
                                            if ($task->status === 3) {
                                                $status = 'success';
                                            } else {
                                                $status = 'warning';
                                            }
                                        } else if($file->status['code'] == 'approved'){
                                            $status = 'success';
                                        } else if($file->status['code'] == 'rejected'){
                                            $status = 'danger';
                                        } else {
                                            $status = 'info';
                                        }
                                    @endphp
                                    <div>
                                        <table>
                                            <tr>
                                                <td><b>Status</b></td>
                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                <td> <span class="badge badge-{{ $status }}">{{ $file->status['code'] == 'waiting' && $task->status === 3 ? 'Selesai' : $file->status['name'] }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><b>Catatan</b></td>
                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                <td>{{ $file['notes'] ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </li>
                    @empty
                        <center>Belum ada dokumen diunggah</center>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="side-container">
            @if(Auth::user()->role->code === 'level_1' && $task->status !== 3)
                <a
                    href="{{ url('/tasks/'.encrypt($task->id).'/approve/task') }}"
                    class="btn btn-success btn-block mb-2 js-swal-confirm-href"
                    title="Apakah anda yakin untuk menyelesaikan tugas ini ?"
                    data-success_text="Tugas selesai"
                >Selesaikan Tugas</a>
            @endif
            <div class="block block-rounded block-file">
                <div class="block-header">
                    <h3 class="block-title">Info Tugas</h3>
                </div>
                <div class="block-content block-content-full pt-0">
                    <div class="row push mb-0">
                        <div class="col-lg-12">
                            <div class="form-group mb-0">
                                <label>Kategori</label>
                                <span class="float-right">{{ $task->category->name ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group mb-0">
                                <label>Riwayat File</label>
                                <span class="badge {{ $task->is_history_file_active === 1 ? 'badge-success' : 'badge-danger' }} float-right">{{ $task->is_history_file_active === 1 ? 'Aktif' : 'Tidak Aktif' }}</span>
                            </div>
                        </div>
                    </div>
                    @if ($default_file)
                    <div class="row push mb-0">
                        <div class="col-lg-12">
                            <div class="form-group mb-0">
                                <label>Nama File</label>
                                <span class="float-right">{{ $default_file->original_name }}</span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <a href="{{ route('download') }}?file={{ encrypt($default_file->id) }}&type=download" target="_blank" class="btn btn-info btn-block">Unduh Contoh File</a>
                        </div>
                    </div>
                    @else
                        <b>Tidak Ada Contoh Dokumen</b>
                    @endif
                </div>
            </div>
            <div class="block block-rounded block-assign">
                <div class="block-header">
                    <h3 class="block-title">Petugas</h3>
                </div>
                <div class="block-content block-content-full pt-0">
                    <ul style="padding-left: 20px" class="mb-0">
                        @forelse ($task->responsible_person as $responsible_person)
                            <li>{{ $responsible_person->name }}</li>
                        @empty
                            <li>Semua Staf</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal" id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="preview-modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="block block-rounded block-themed block-transparent mb-0">
                <div class="block-header bg-primary-dark">
                    <h3 class="block-title preview-title"></h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
                        <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content font-size-sm">
                    <div class="embed-responsive embed-responsive-16by9 preview-content">
                        <embed class="embed-responsive-item preview-src" src="" allowfullscreen></embed>
                    </div>
                </div>
                <div class="block-content block-content-full text-right border-top">
                    <button type="button" class="btn btn-alt-primary mr-1" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END Preview Modal -->

<form action="" method="POST" id="approveForm">
    @csrf
    <input type="hidden" style="display: none;" id="notes_value" value="">
    <input type="submit" style="display: none;">
</form>

<form action="" method="POST" id="rejectForm">
    @csrf
    <input type="submit" style="display: none;">
</form>
@endsection

@section('js_after')
    <script type="text/javascript">
        $('#task_file').change(function() {
            if($(this)[0]) {
                $('#custom_name').prop('disabled', false);
                $('#description').prop('disabled', false);
                $('.btn-file').prop('disabled', false);
            }
        });
        $('#note_name').keyup(function() {
            if($('#note_name').val() && $('#note_content').val()) {
                $('#btn-note').prop('disabled', false);
            }
            else {
                $('#btn-note').prop('disabled', true);
            }
        });
        $('#note_content').keyup(function() {
            if($('#note_name').val() && $('#note_content').val()) {
                $('#btn-note').prop('disabled', false);
            }
            else {
                $('#btn-note').prop('disabled', true);
            }
        });
        $('#preview-modal').on('show.bs.modal', function(e) {
            var file = $(e.relatedTarget).data('file');
            $('.preview-title').html(file.original_name);
            $('.preview-src').attr('src', file.file_url);
        });
    </script>
@endsection