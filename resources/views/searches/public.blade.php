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
        <form action="{{ route('search.public') }}" method="GET">
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
            </ul>
            <div class="block-content tab-content overflow-hidden">
                <!-- Files -->
                <div class="tab-pane fade fade-up show active" id="search-files" role="tabpanel">
                    @if (isset($files))
                        @if (app('request')->input('q'))
                        <div class="font-size-h4 font-w600 p-2 mb-4 border-left border-4x border-primary bg-body-light">
                            <span class="text-primary font-w700">{{ isset($files) ? count($files) : 0 }}</span> File ditemukan
                        </div>                            
                        @endif

                        @if (count($files))
                        <table class="table table-striped table-vcenter js-dataTable-simple">
                            <thead>
                                <tr>
                                    <th class="d-none d-lg-table-cell text-center" style="width: 40%;">File</th>
                                    <th class="d-none d-lg-table-cell text-center defaultSort" style="width: 15%;">Tanggal</th>
                                    <th class="text-center" style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($files as $file)
                                <tr>
                                    <td>
                                        <h4 class="h5 mt-3 mb-2">
                                            <i class="far fa-file"></i>
                                            <a href="{{ route('download.public') }}?file={{ encrypt($file->id) }}&type=download&type_file={{ $file->type }}">{{ $file->original_name }}</a>
                                        </h4>
                                        <p class="d-none d-sm-block text-muted">
                                            {{ $file->description }}
                                        </p>
                                    </td>
                                    <td class="d-none d-lg-table-cell text-center" data-order="{{strtotime($file->created_at)}}">
                                        {{ \Carbon\Carbon::parse($file->created_at)->isoFormat('D MMMM YYYY') }}
                                    </td>
                                    <td class="font-size-xl text-center font-w600">
                                        <a class="btn btn-sm btn-primary"
                                            href="{{ route('download.public') }}?file={{ encrypt($file->id) }}&type=download&type_file={{ $file->type }}"
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

@section('js_after')
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
@endsection