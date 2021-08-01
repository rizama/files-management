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
<div class="row push">
    <div class="col-lg-8">
        <div class="block block-rounded">
            <div class="block-header">
                <h3 class="block-title">Unggah Tugas</h3>
            </div>
            <div class="block-content block-content-full">
                <div class="col-lg-12">
                    <div class="form-group row">
                        <label class="col-lg-5 col-form-label">File</label>
                        <div class="custom-file col-lg-7">
                            <!-- Populating custom file input label with the selected filename (data-toggle="custom-file-input" is initialized in Helpers.coreBootstrapCustomFileInput()) -->
                            <input type="file" class="custom-file-input" data-toggle="custom-file-input" id="default_file" name="default_file" lang="id">
                            <label class="custom-file-label" for="default_file"></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="custom_name" class="col-lg-5 col-form-label">Ubah Nama File</label>
                        <input type="text" class="form-control col-lg-7 @error('custom_name') is-invalid @enderror" id="custom_name" name="custom_name" placeholder="Masukan Nama File" disabled>
                        @error('custom_name')
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
        </div>
        <div class="block block-rounded">
            <div class="block-header">
                <h3 class="block-title">Riwayat File</h3>
            </div>
            <div class="block-content block-content-full">
                <ul class="timeline timeline-alt py-0 timeline-file">
                    <li class="timeline-event">
                        <div class="timeline-event-icon bg-default">
                            <i class="fa fa-file-alt"></i>
                        </div>
                        <div class="timeline-event-block block invisible" data-toggle="appear">
                            <div class="block-header">
                                <h3 class="block-title">Mochamad Rizky Purnama</h3>
                                <div class="block-options">
                                    <div class="timeline-event-time block-options-item font-size-sm">
                                        baru saja
                                    </div>
                                </div>
                            </div>
                            <div class="block-content pt-0">
                                <p>
                                    Ini deskripsi file yang di upload
                                </p>
                                <a href="http://ciptakarya.pu.go.id/profil/profil/barat/jabar/bandung.pdf" target="_blank" class="btn btn-secondary">Unduh File</a>
                                <div class="mt-2">
                                    <div>
                                        <b>Status :</b>
                                        <span class="badge badge-success">Disetujui</span>
                                    </div>
                                    <b>Catatan :</b>
                                    <p class="mb-0"> Ini catatan setelah disetujui </p>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="timeline-event">
                        <div class="timeline-event-icon bg-default">
                            <i class="fa fa-file-alt"></i>
                        </div>
                        <div class="timeline-event-block block invisible" data-toggle="appear">
                            <div class="block-header">
                                <h3 class="block-title">Mochamad Rizky Purnama</h3>
                                <div class="block-options">
                                    <div class="timeline-event-time block-options-item font-size-sm">
                                        baru saja
                                    </div>
                                </div>
                            </div>
                            <div class="block-content pt-0">
                                <p>
                                    Ini deskripsi file yang di upload
                                </p>
                                <a href="http://ciptakarya.pu.go.id/profil/profil/barat/jabar/bandung.pdf" target="_blank" class="btn btn-secondary">Unduh File</a>
                                <div class="accordion mt-2" id="accordionExample">
                                    <div class="card">
                                      <div class="card-header p-0" id="headingOne">
                                        <h2 class="mb-0">
                                          <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                              File ini perlu disetujui
                                          </button>
                                        </h2>
                                      </div>
                                  
                                      <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                        <div class="card-body">
                                            <div class="row push">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label for="name">Catatan</label>
                                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Masukan Catatan terhadap File ini" required>
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
                            </div>
                        </div>
                    </li>
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
                    <div class="col-lg-12">
                        <a href="http://ciptakarya.pu.go.id/profil/profil/barat/jabar/bandung.pdf" target="_blank" class="btn btn-light btn-block">Unduh Contoh File</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="block block-rounded block-assign">
            <div class="block-header">
                <h3 class="block-title">Ditugaskan ke</h3>
            </div>
            <div class="block-content block-content-full pt-0">
                <ul style="padding-left: 20px" class="mb-0">
                    @foreach ($task->responsible_person as $responsible_person)
                        <li>{{ $responsible_person->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js_after')
    <script>
        $('#default_file').change(function() {
            if($(this)[0]) {
                $('#custom_name').prop('disabled', false);
                $('.btn-submit').prop('disabled', false);
            }
        });
    </script>
@endsection