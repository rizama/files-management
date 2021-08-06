@extends('layouts.app')

@section('title')
    Kategori Manajemen - {{ env('APP_NAME') }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css') }}">
@endsection

@section('page-title')
    Manajemen Kategori
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

    @if (session()->has('category.created'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{ session('category.created') }}</p>
        </div>
    @endif

    @if (session()->has('category.updated'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{ session('category.updated') }}</p>
        </div>
    @endif

    @if (session()->has('category.deleted'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{ session('category.deleted') }}</p>
        </div>
    @endif


    <!-- Dynamic Table Full -->
    <div class="block block-rounded">
        <div class="block-header">
            <h2 class="block-title">Daftar Kategori</h2>
            <a class="btn btn-primary pull-right btn-sm" href="{{ url('/categories/create') }}">Tambah Kategori</a>
        </div>
        <div class="block-content block-content-full">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">No</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th class="disable-sorting">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td class="text-center font-size-sm">{{ $loop->index + 1 }}</td>
                            <td class="font-w600 font-size-sm">
                                {{ $category->name }}
                            </td>
                            <td class="d-none d-sm-table-cell font-size-sm">
                                {{ $category->description }}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-warning"
                                        href="{{ url('/categories/edit/').'/'.encrypt($category->id) }}"
                                        data-animation="true" data-toggle="tooltip"
                                        title="Ubah Kategori" data-original-title="Ubah Kategori"
                                        style="margin-right: 3px"
                                    >
                                        <i class="fa fa-fw fa-pencil-alt"></i>
                                    </a>
                                    <a class="btn btn-sm btn-danger js-swal-confirm"
                                        href="{{ route('categories.destroy', encrypt($category->id)) }}"
                                        data-toggle="tooltip" title="" data-original-title="Hapus Kategori">
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
