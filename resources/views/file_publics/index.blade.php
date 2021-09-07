@extends('layouts.app')

@section('title')
    Manajemen Dokumen Umum - {{ env('APP_NAME') }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css') }}">
@endsection

@section('page-title')
    Manajemen Dokumen Umum
@endsection

@section('content')
    @if (session()->has('flash_notification.success'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{ session('flash_notification.success') }}</p>
        </div>
    @endif

    @if (session()->has('file_public.created'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{ session('file_public.created') }}</p>
        </div>
    @endif

    @if (session()->has('file_public.updated'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{ session('file_public.updated') }}</p>
        </div>
    @endif

    @if (session()->has('file_public.deleted'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{ session('file_public.deleted') }}</p>
        </div>
    @endif


    <!-- Dynamic Table Full -->
    <div class="block block-rounded">
        <div class="block-header">
            <h2 class="block-title">Daftar Dokumen Umum</h2>
            <a class="btn btn-primary pull-right btn-sm" href="{{ url('/file_publics/create') }}">Tambah Dokumen Umum</a>
        </div>
        <div class="block-content block-content-full">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-no-defaultSort">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">No</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Pengunggah</th>
                        <th>Tanggal diunggah</th>
                        <th class="disable-sorting">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($file_publics as $file_public)
                        <tr>
                            <td class="text-center font-size-sm">{{ $loop->index + 1 }}</td>
                            <td class="font-w600 font-size-sm">
                                {{ $file_public->original_name }}
                            </td>
                            <td class="d-none d-sm-table-cell font-size-sm">
                                {{ $file_public->description }}
                            </td>
                            <td>
                                {{ $file_public->user->name }}
                            </td>
                            <td class="font-size-sm">{{ \Carbon\Carbon::parse($file_public->created_at)->isoFormat('D MMMM Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-primary"
                                        href="{{ route('download') }}?file={{ encrypt($file_public->id) }}&type=download"
                                        data-animation="true" data-toggle="tooltip"
                                        title="Unduh Dokumen" data-original-title="Unduh File"
                                        style="margin-right: 3px"
                                    >
                                        <i class="fa fa-fw fa-file-download"></i>
                                    </a>
                                    <a class="btn btn-sm btn-warning"
                                        href="{{ url('/file_publics/edit/').'/'.encrypt($file_public->id) }}"
                                        data-animation="true" data-toggle="tooltip"
                                        title="Ubah Dokumen" data-original-title="Ubah Dokumen"
                                        style="margin-right: 3px"
                                    >
                                        <i class="fa fa-fw fa-pencil-alt"></i>
                                    </a>
                                    <a class="btn btn-sm btn-danger js-swal-confirm"
                                        href="{{ route('file_publics.destroy', encrypt($file_public->id)) }}"
                                        data-toggle="tooltip" title="" data-original-title="Hapus Dokumen">
                                        <i class="fa fa-fw fa-times"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- END Dynamic Table Full -->

@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
@endsection
