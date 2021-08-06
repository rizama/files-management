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
        <form action="/" method="POST">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Cari..">
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
                    <div class="font-size-h4 font-w600 p-2 mb-4 border-left border-4x border-primary bg-body-light">
                        <span class="text-primary font-w700">1</span> Tugas ditemukan
                    </div>
                    <table class="table table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Tugas</th>
                                <th class="d-none d-lg-table-cell text-center" style="width: 15%;">Status</th>
                                {{-- <th class="d-none d-lg-table-cell text-center" style="width: 15%;">Sales</th> --}}
                                {{-- <th class="text-center" style="width: 20%;">Earnings</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <h4 class="h5 mt-3 mb-2">
                                        <a href="javascript:void(0)">Tugas Menggambar</a>
                                    </h4>
                                    <p class="d-none d-sm-block text-muted">
                                        Ini Deskripsi tugas menggambar
                                    </p>
                                </td>
                                <td class="d-none d-lg-table-cell text-center">
                                    <span class="badge badge-success">Disetujui</span>
                                </td>
                                {{-- <td class="d-none d-lg-table-cell font-size-xl text-center font-w600">1603</td> --}}
                                {{-- <td class="font-size-xl text-center font-w600">$ 35,287</td> --}}
                            </tr>
                        </tbody>
                    </table>
                    <nav aria-label="tasks Search Navigation">
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
                    </nav>
                </div>
                <!-- END tasks -->
                <!-- Files -->
                <div class="tab-pane fade fade-up show active" id="search-files" role="tabpanel">
                    <div class="font-size-h4 font-w600 p-2 mb-4 border-left border-4x border-primary bg-body-light">
                        <span class="text-primary font-w700">1</span> File ditemukan
                    </div>
                    <table class="table table-striped table-vcenter">
                        <thead>
                            <tr>
                                <th style="width: 50%;">File</th>
                                <th class="d-none d-lg-table-cell text-center" style="width: 15%;">Tanggal</th>
                                <th class="d-none d-lg-table-cell text-center" style="width: 15%;">Pengunggah</th>
                                <th class="text-center" style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <h4 class="h5 mt-3 mb-2">
                                        <a href="javascript:void(0)">File Sam</a>
                                    </h4>
                                    <p class="d-none d-sm-block text-muted">
                                        Ini Deskripsi File
                                    </p>
                                </td>
                                <td class="d-none d-lg-table-cell text-center">
                                    30 Juli 2021
                                </td>
                                <td class="d-none d-lg-table-cell font-size-xl text-center font-w600">Mochamad Rizky Purnama</td>
                                <td class="font-size-xl text-center font-w600">
                                    <a class="btn btn-sm btn-primary"
                                        {{-- href="{{ url('/tasks/show/').'/'.encrypt($task->id) }}" --}}
                                        data-animation="true" data-toggle="tooltip"
                                        title="Unduh File" data-original-title="Unduh File"
                                    >
                                        <i class="fa fa-fw fa-file-download"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <nav aria-label="tasks Search Navigation">
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
                    </nav>
                </div>
                <!-- END Files -->
            </div>
        </div>
        <!-- END Results -->
    </div>
    <!-- END Page Content -->

@endsection
