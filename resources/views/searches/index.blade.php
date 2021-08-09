@extends('layouts.app')

@section('title')
    Pencarian - {{ env('APP_NAME') }}
@endsection

@section('page-title')
    Pencarian
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css') }}">
@endsection


@section('content')

    <!-- Search -->
    <div class="content">
        <form action="{{ route('search.index') }}" method="GET">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Cari.." name="q" value="{{ old('q', app('request')->input('q')) }}">
                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="fa fa-fw fa-search"></i>
                    </span>
                </div>
            </div>
        </form>
    </div>
    <!-- END Search -->

    <!-- Page Content -->
    <div class="content">
        <!-- Results -->
        <div class="block block-rounded overflow-hidden">
            <ul class="nav nav-tabs nav-tabs-block" data-toggle="tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#search-files">File</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#search-tasks">Tugas</a>
                </li>
            </ul>
            <div class="block-content tab-content overflow-hidden">
                <!-- tasks -->
                <div class="tab-pane fade fade-up" id="search-tasks" role="tabpanel">
                    @if (isset($tasks))
                    @if (app('request')->input('q'))
                    <div class="font-size-h4 font-w600 p-2 mb-4 border-left border-4x border-primary bg-body-light">
                        <span class="text-primary font-w700">{{ isset($tasks) ? count($tasks) : 0 }}</span> Tugas ditemukan
                    </div>
                    @endif
                        @if (count($tasks))
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Tugas</th>
                                    <th class="d-none d-lg-table-cell text-center" style="width: 15%;">Pembuat</th>
                                    <th class="text-center" style="width: 20%;">Petugas</th>
                                    <th class="d-none d-lg-table-cell text-center" style="width: 15%;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                <tr>
                                    <td>
                                        <h4 class="h5 mt-3 mb-2">
                                            <a href="javascript:void(0)">{{ $task->name }}</a>
                                        </h4>
                                        <p class="d-none d-sm-block text-muted">
                                            {{ $task->description }}
                                        </p>
                                    </td>
                                    @php
                                        if($task->status == 1){
                                            $status = 'warning';
                                        } else {
                                            $status = 'success';
                                        }
                                    @endphp
                                    <td class="d-none d-lg-table-cell font-size-xl text-center font-w600">{{ $task->user->name }}</td>
                                    <td class="font-size-xl text-center font-w600">
                                        @forelse ($task->responsible_person as $responsible_person)
                                            {{ $responsible_person->name }}{{ $loop->last ? '' : ', ' }}
                                        @empty
                                            Semua Staf
                                        @endforelse
                                    </td>
                                    <td class="d-none d-lg-table-cell text-center">
                                        <span class="badge badge-{{ $status }}">{{ $task->status == 3 ? 'Disetujui' : 'Dikerjakan'}}</span>
                                    </td>
 
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- <nav aria-label="tasks Search Navigation">
                            <ul class="pagination pagination-sm">
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0)" tabindex="-1" aria-label="Previous">
                                        Prev
                                    </a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="javascript:void(0)">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0)">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0)">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0)">4</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0)" aria-label="Next">
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </nav> --}}
                        @else
                        <div class="mb-3">
                            <center>Tidak ada hasil pencarian</center>
                        </div>
                        @endif
                    @else
                    <div class="mb-3">
                        <center>Belum melakukan pencarian</center>
                    </div>
                    @endif
                </div>
                <!-- END tasks -->
                <!-- Files -->
                <div class="tab-pane fade fade-up show active" id="search-files" role="tabpanel">
                    @if (isset($files))
                        @if (app('request')->input('q'))
                        <div class="font-size-h4 font-w600 p-2 mb-4 border-left border-4x border-primary bg-body-light">
                            <span class="text-primary font-w700">{{ isset($files) ? count($files) : 0 }}</span> File ditemukan
                        </div>                            
                        @endif

                        @if (count($files))
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th class="d-none d-lg-table-cell text-center" style="width: 40%;">File</th>
                                    <th class="d-none d-lg-table-cell text-center" style="width: 15%;">Tugas</th>
                                    <th class="d-none d-lg-table-cell text-center" style="width: 15%;">Tanggal</th>
                                    <th class="d-none d-lg-table-cell text-center" style="width: 15%;">Pengunggah</th>
                                    <th class="text-center" style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($files as $file)
                                <tr>
                                    <td>
                                        <h4 class="h5 mt-3 mb-2">
                                            <a href="javascript:void(0)">{{ $file->original_name }}</a>
                                        </h4>
                                        <p class="d-none d-sm-block text-muted">
                                            {{ $file->description }}
                                        </p>
                                    </td>
                                    <td class="d-none d-lg-table-cell text-center">
                                        {{ $file->task->name }}
                                    </td>
                                    <td class="d-none d-lg-table-cell text-center">
                                        {{ \Carbon\Carbon::parse($file->created_at)->isoFormat('D MMMM YYYY') }}
                                    </td>
                                    <td class="d-none d-lg-table-cell font-size-xl text-center font-w600">{{ $file->user->name }}</td>
                                    <td class="font-size-xl text-center font-w600">
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('download') }}?file={{ encrypt($file->id) }}&type=download"
                                            data-animation="true" data-toggle="tooltip"
                                            title="Unduh File" data-original-title="Unduh File"
                                        >
                                            <i class="fa fa-fw fa-file-download"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- <nav aria-label="tasks Search Navigation">
                            <ul class="pagination pagination-sm">
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0)" tabindex="-1" aria-label="Previous">
                                        Prev
                                    </a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="javascript:void(0)">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0)">2</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0)">3</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0)">4</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="javascript:void(0)" aria-label="Next">
                                        Next
                                    </a>
                                </li>
                            </ul>
                        </nav> --}}
                        @else   
                            <div class="mb-3">
                                <center>Tidak ada hasil pencarian</center>
                            </div>
                        @endif 
                    @else
                        <div class="mb-3">
                            <center>Belum melakukan pencarian</center>
                        </div>
                    @endif
                </div>
                <!-- END Files -->
            </div>
        </div>
        <!-- END Results -->
    </div>
    <!-- END Page Content -->

@endsection
