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

@if (session()->has('file.deleted'))
<div class="alert alert-warning alert-dismissable" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <p class="mb-0">{{ session('file.deleted') }}</p>
</div>
@endif

<div class="row push">
    <div class="col-lg-8">
        <div class="block block-rounded">
            <div class="block-header">
                <a href="{{ url()->previous() }}" class="btn btn-alt-secondary mr-2"><i class="fa fa-arrow-left mr-1"></i> Kembali</a>
                <h3 class="block-title text-right text-bold">
                    Detail
                </h3>
            </div>
            <div class="block-content tab-content pt-2">
                <div class="form-group mb-0">
                    <label>Pengunggah</label>
                    <p>{{ $task->user->name }}</p>
                </div>
                <div class="form-group mb-0">
                    <label>Deskripsi</label>
                    <p>{{ $task->description ?? '-' }}</p>
                </div>
                <div class="form-group mb-0">
                    <label>Tenggat Waktu</label>
                    <p>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->isoFormat('D MMMM Y, HH:mm') : '-' }}</p>
                </div>
            </div>
        </div>
        @if ($task->assign_to)
            @if($task->status != 3 && ( $task->assign_to == 'all' || (in_array(Auth::user()->id, json_decode($task->assign_to)) || json_decode($task->assign_to) == [])))
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
                                            <label class="col-lg-3 col-form-label">Dokumen</label>
                                            <div class="custom-file col-lg-9">
                                                <!-- Populating custom dokumen input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                                                <input type="file" class="custom-file-input @error('task_file') is-invalid @enderror" data-toggle="custom-file-input" id="task_file" name="task_file" lang="id" accept="{{ config('app.accept_file_fe') }}">
                                                <label class="custom-file-label" for="task_file"></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="custom_name" class="col-lg-3 col-form-label">Ubah Nama Dokumen</label>
                                            <input type="text" class="form-control col-lg-9 @error('custom_name') is-invalid @enderror" id="custom_name" name="custom_name" placeholder="Masukan Nama Dokumen" disabled>
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
                                            <input type="text" class="form-control @error('note_name') is-invalid @enderror" id="note_name" name="note_name" placeholder="Masukan Nama Dokumen">
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
        @endif
        <div class="block block-rounded">
            <div class="block-header">
                <h3 class="block-title">Riwayat Dokumen</h3>
            </div>
            <div class="block-content block-content-full">
                <ul class="timeline timeline-alt py-0 timeline-file">
                    @forelse ($task->files as $key => $file)
                    <li class="timeline-event">
                        {{-- <div class="timeline-event-icon bg-default"> --}}
                            @if (App\Http\Controllers\TaskController::mime2ext($file->mime_type) == 'pdf')
                            <div class="timeline-event-icon bg-danger">
                                <i class="fa fa-file-pdf"></i>
                            </div>
                            @elseif(in_array(App\Http\Controllers\TaskController::mime2ext($file->mime_type), ['txt', 'rtf']))
                            <div class="timeline-event-icon bg-default">    
                                <i class="fa fa-file-alt"></i>
                            </div>
                            @elseif(App\Http\Controllers\TaskController::mime2ext($file->mime_type) == 'csv')   
                            <div class="timeline-event-icon bg-success">    
                                <i class="fa fa-file-csv"></i>
                            </div>
                            @elseif(in_array(App\Http\Controllers\TaskController::mime2ext($file->mime_type), ['xls', 'xlsx']))
                            <div class="timeline-event-icon bg-success">    
                                <i class="fa fa-file-excel"></i>
                            </div>
                            @elseif(in_array(App\Http\Controllers\TaskController::mime2ext($file->mime_type), ['png', 'jpeg', 'jpg', 'bmp']))
                            <div class="timeline-event-icon bg-amethyst-light">    
                                <i class="fa fa-file-image"></i>
                            </div>
                            @elseif(in_array(App\Http\Controllers\TaskController::mime2ext($file->mime_type), ['docx', 'doc']))
                            <div class="timeline-event-icon bg-default">    
                                <i class="fa fa-file-word"></i>
                            </div>
                            @elseif(in_array(App\Http\Controllers\TaskController::mime2ext($file->mime_type), ['ppt', 'pptx']))
                            <div class="timeline-event-icon bg-warning">    
                                <i class="fa fa-file-powerpoint"></i>
                            </div>
                            @elseif(in_array(App\Http\Controllers\TaskController::mime2ext($file->mime_type), ['rar', 'zip']))
                            <div class="timeline-event-icon bg-city-light">    
                                <i class="fa fa-file-archive"></i>
                            </div>
                            @else
                                <i class="fa fa-file"></i>
                            @endif
                        {{-- </div> --}}
                        <div class="timeline-event-block block invisible" data-toggle="appear">
                            <div class="block-header">
                                <h3 class="block-title"><small>Pengunggah </small>{{ $file->user['name']}}</h3>
                                <div class="block-options">
                                    <div class="timeline-event-time block-options-item font-size-sm text-bold">
                                        {{ \Carbon\Carbon::parse($file['created_at'])->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            <div class="block-content pt-0">
                                <p class="block-title clamp clamp-5">
                                    <small>Deskripsi</small>{{ $file['description'] ?? '-' }}
                                </p>
                                <div class="col-lg-12">
                                    <a href="{{ route('download') }}?file={{ encrypt($file->id) }}&type=download" class="btn btn-outline-primary mb-2" title="{{$file->original_name}}.{{ App\Http\Controllers\TaskController::mime2ext($file->mime_type) }}"><i class="fa fa-download"></i> Unduh</a>
                                    @if(in_array(App\Http\Controllers\TaskController::mime2ext($file->mime_type), ['png', 'jpeg', 'jpg', 'pdf', 'bmp', 'txt']))
                                        <button type="button" class="btn btn-outline-info push mb-2" data-toggle="modal" data-target="#preview-modal" data-file="{{$file}}" data-ext="{{App\Http\Controllers\TaskController::mime2ext($file->mime_type)}}" id="preview-btn-modal"><i class="fa fa-eye"></i> Pratinjau</button>
                                    @endif
                                    @if ($file->status['code'] == 'waiting' && $task->status != 3 && (Auth::user()->id == $file->created_by || Auth::user()->id == $task->created_by))
                                        <a
                                            class="btn btn-outline-danger reject-file js-swal-confirm-with-form push mb-2"
                                            style="float: right;"
                                            data-type_button="reject"
                                            href="{{ route('file.delete', encrypt($file->id)) }}"
                                            title="Apakah anda yakin untuk menghapus dokumen ini ?"
                                            data-caption="{{$file->original_name ?? ''}}.{{ App\Http\Controllers\TaskController::mime2ext($file->mime_type) }}{{$file->description ? ', Deskripsi: '.$file->description : ''}}"
                                            data-form_id="delete_file"
                                            data-success_text="Dokumen Berhasil Dihapus"
                                        ><i class="fa fa-trash"></i> Hapus</a>
                                    @endif
                                </div>
                                <div class="clearfix"></div>
                                @if ($file->status['code'] == 'waiting' && $task->status != 3 && ($key == 0 || $task->is_confirm_all) && Auth::user()->id == $task->created_by)
                                    @if (Auth::user()->role->code == 'level_1')
                                    <div class="accordion mt-2" id="accordionExample">
                                        <div class="card">
                                            <div class="card-header p-0" id="headingOne">
                                                <h2 class="mb-0">
                                                    <button class="btn btn-link btn-block text-left text-bold" type="button" data-toggle="collapse" data-target="#collapse-{{ $key }}" aria-expanded="true" aria-controls="collapse-{{ $key }}">
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
                                                                    title="Apakah anda yakin untuk menyetujui dokumen ini ?"
                                                                    data-caption="{{$file->original_name ?? ''}}.{{ App\Http\Controllers\TaskController::mime2ext($file->mime_type) }} {{$file->description ? ', Deskripsi: '.$file->description : ''}}"
                                                                    data-form_id="verification"
                                                                    data-success_text="Dokumen Berhasil Disetujui"
                                                                ><i class="fa fa-check"></i> Setujui</a>
                                                                <a
                                                                    class="btn btn-danger reject-file js-swal-confirm-with-form"
                                                                    data-type_button="reject"
                                                                    href="{{ route('tasks.reject', encrypt($file->id)) }}"
                                                                    title="Apakah anda yakin untuk menolak dokumen ini ?"
                                                                    data-caption="{{$file->original_name ?? ''}}.{{ App\Http\Controllers\TaskController::mime2ext($file->mime_type) }}{{$file->description ? ', Deskripsi: '.$file->description : ''}}"
                                                                    data-form_id="verification"
                                                                    data-success_text="Dokumen Berhasil Ditolak"
                                                                ><i class="fa fa-times"></i> Tolak</a>
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
                                @elseif($file->status['code'] == 'waiting' && $task->status != 3 && $key != 0)
                                @else
                                    <div class="mt-2">
                                        @php
                                            if($file->status['code'] == 'waiting'){
                                                if ($task->status == 3) {
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
                                                    <td> <span class="badge badge-{{ $status }}">{{ $file->status['code'] == 'waiting' && $task->status == 3 ? 'Selesai' : $file->status['name'] }}</span></td>
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
            @if(Auth::user()->id == $task->created_by && $task->status != 3)
                <a
                    href="{{ url('/tasks/'.encrypt($task->id).'/approve/task') }}"
                    class="btn btn-success btn-block mb-2 js-swal-confirm-href"
                    title="Apakah anda yakin untuk menyelesaikan tugas ini ?"
                    data-success_text="Tugas selesai"
                ><i class="fa fa-check-square"></i> Selesaikan Tugas</a>
            @endif
            <div class="block block-rounded block-file">
                <div class="block-header">
                    <h3 class="block-title text-bold">Info Tugas</h3>
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
                                <label>Riwayat Dokumen</label>
                                <span class="badge {{ $task->is_history_file_active == 1 ? 'badge-success' : 'badge-danger' }} float-right">{{ $task->is_history_file_active == 1 ? 'Aktif' : 'Tidak Aktif' }}</span>
                            </div>
                        </div>
                    </div>
                    @if ($default_file)
                    <div class="row push mb-0">
                        <div class="col-lg-12">
                            <div class="form-group mb-0">
                                <label>Nama Dokumen</label>
                                <span class="float-right">{{ $default_file->original_name }}</span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <a href="{{ route('download') }}?file={{ encrypt($default_file->id) }}&type=download" target="_blank" class="btn btn-info btn-block">Unduh Contoh Dokumen</a>
                        </div>
                    </div>
                    @else
                        <b>Tidak Ada Contoh Dokumen</b>
                    @endif
                </div>
            </div>
            <div class="block block-rounded block-assign">
                <div class="block-header">
                    <h3 class="block-title text-bold">Staf Penanggung Jawab</h3>
                </div>
                <div class="block-content block-content-full pt-0">
                    <ul style="padding-left: 20px" class="mb-0">
                        @forelse ($task->responsible_person as $responsible_person)
                            <li>{{ $responsible_person->name }}</li>
                        @empty
                            @if ($task->assign_to == 'all')
                                <li>Semua Staf</li>                                
                            @else
                                <li> - </li>
                            @endif
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal" id="preview-modal" tabindex="-1" role="dialog" aria-labelledby="preview-modal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
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
                <div class="block-content font-size-sm text-center">
                    <img src="" class="img-fluid preview-src-img" />
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

<form action="" method="GET" id="delete_file" style="display: inline-block; float: right;"><form>

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
        $('#preview-modal').on('shown.bs.modal', function(e) {
            var file = $(e.relatedTarget).data('file');
            var ext = $(e.relatedTarget).data('ext');
            $('.preview-title').html(file.original_name);
            if(['pdf', 'txt'].includes(ext)) {
                $('.embed-responsive').removeClass('d-none');
                $('.preview-src-img').addClass('d-none');
                $('.preview-src').attr('src', file.file_url);
            } else {
                $('.preview-src-img').removeClass('d-none');
                $('.embed-responsive').addClass('d-none');
                $('.preview-src-img').attr('src', file.file_url);
            }
        });
        $('#preview-modal').on('hidden.bs.modal', function(e) {
            $('.preview-src').attr('src', '#');
            $('.preview-src-img').attr('src', '#');
        });
    </script>
@endsection