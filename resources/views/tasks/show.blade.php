@extends('layouts.app')

@section('title')
    Manajemen Tugas - SIMANTAP
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

<div class="row push">
    <div class="col-lg-8">
        <div class="block block-rounded">
            <div class="block-header">
                <h3 class="block-title">Unggah Tugas</h3>
            </div>
            <form action="{{ route('tasks.send_file', encrypt($task->id)) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="block-content block-content-full">
                    <div class="col-lg-12">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">File</label>
                            <div class="custom-file col-lg-9">
                                <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                                <input type="file" class="custom-file-input @error('task_file') is-invalid @enderror" data-toggle="custom-file-input" id="task_file" name="task_file" lang="id" >
                                <label class="custom-file-label" for="task_file"></label>
                                @error('task_file')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="custom_name" class="col-lg-3 col-form-label">Ubah Nama File</label>
                            <input type="text" class="form-control col-lg-9 @error('custom_name') is-invalid @enderror" id="custom_name" name="custom_name" placeholder="Masukan Nama File" disabled>
                            @error('custom_name')
                                <span style="color: red">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-lg-3 col-form-label">Deskripsi</label>
                            <textarea name="description" id="description" class="form-control col-lg-9 @error('description') is-invalid @enderror" placeholder="Masukan Deskripsi" disabled></textarea>
                            @error('description')
                                <span style="color: red">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-right">
                            <button type="submit" class="btn btn-primary btn-submit" data-toggle="click-ripple" disabled>Unggah</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
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
                                <h3 class="block-title">{{ $file->user['name']}}</h3>
                                <div class="block-options">
                                    <div class="timeline-event-time block-options-item font-size-sm">
                                        {{ \Carbon\Carbon::parse($file['created_at'])->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            <div class="block-content pt-0">
                                <p>
                                    {{ $file['description'] }}
                                </p>
                                <a href="http://ciptakarya.pu.go.id/profil/profil/barat/jabar/bandung.pdf" target="_blank" class="btn btn-secondary">Unduh File</a>
                                
                                @if ($file->status['code'] == 'waiting')
                                <div class="accordion mt-2" id="accordionExample">
                                    <div class="card">
                                        <div class="card-header p-0" id="headingOne">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse-{{ $key }}" aria-expanded="true" aria-controls="collapse-{{ $key }}">
                                                    Konfirmasi Dokumen
                                                </button>
                                            </h2>
                                        </div>
                                        <div id="collapse-{{ $key }}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <div class="row push">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label for="name">Catatan</label>
                                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Tambahkan Catatan (Opsional)" required>
                                                            @error('name')
                                                                <span style="color: red">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 text-right">
                                                        <button type="submit" class="btn btn-success" data-toggle="click-ripple">Setujui</button>
                                                        <a href="#" class="btn btn-danger">Tolak</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @else 
                                <div class="mt-2">
                                    @php
                                        if($file->status['code'] == 'waiting'){
                                            $status = 'warning';
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
                                                <td> <span class="badge badge-{{ $status }}">{{ $file->status['name'] }}</span></td>
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
        <div class="block block-rounded block-file">
            <div class="block-header">
                <h3 class="block-title">Info File Tugas</h3>
            </div>
            <div class="block-content block-content-full pt-0">
                <div class="row push mb-0">
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
                        <a href="http://ciptakarya.pu.go.id/profil/profil/barat/jabar/bandung.pdf" target="_blank" class="btn btn-light btn-block">Unduh Contoh File</a>
                    </div>
                </div>
                @else
                    <b>Tidak Ada Contoh Dokumen</b>
                @endif
            </div>
        </div>
        <div class="block block-rounded block-assign">
            <div class="block-header">
                <h3 class="block-title">Ditugaskan ke</h3>
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
@endsection

@section('js_after')
    <script>
        $('#task_file').change(function() {
            if($(this)[0]) {
                $('#custom_name').prop('disabled', false);
                $('#description').prop('disabled', false);
                $('.btn-submit').prop('disabled', false);
            }
        });
    </script>
@endsection