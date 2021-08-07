@extends('layouts.app')

@section('title')
    User Manajemen - {{ env('APP_NAME') }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css') }}">
@endsection

@section('page-title')
    Manajemen Tugas
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

    @if (session()->has('user.created'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{ session('user.created') }}</p>
        </div>
    @endif

    @if (session()->has('user.updated'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{ session('user.updated') }}</p>
        </div>
    @endif

    @if (session()->has('user.deleted'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{ session('user.deleted') }}</p>
        </div>
    @endif


    <!-- Dynamic Table Full -->
    <div class="block block-rounded">
        <div class="block-header">
            <h2 class="block-title">Daftar Tugas</h2>
            <a class="btn btn-primary pull-right btn-sm" href="{{ url('/tasks/create') }}">Tambah Tugas</a>
        </div>
        <div class="block-content block-content-full">
            <table class="table table-bordered table-striped table-vcenter js-dataTable-full">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">No</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Riwayat File</th>
                        <th>Ditugaskan Ke</th>
                        <th>Tanggal dibuat</th>
                        <th class="disable-sorting">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td class="text-center font-size-sm">{{ $loop->index + 1 }}</td>
                            <td class="font-w600 font-size-sm">
                                {{ $task->name }}
                            </td>
                            <td class="d-none d-sm-table-cell font-size-sm">
                                {{ $task->description }}
                            </td>
                            <td>
                                <span class="badge {{ $task->is_history_file_active === 1 ? 'badge-success' : 'badge-danger' }}">{{ $task->is_history_file_active === 1 ? 'Aktif' : 'Tidak Aktif' }}</span>
                            </td>
                            <td class="font-w600 font-size-sm">
                                @forelse ($task->responsible_person as $responsible_person)
                                    {{ $responsible_person->name }}{{ $loop->last ? '' : ', ' }}
                                @empty
                                    Semua Staf
                                @endforelse
                            </td>
                            <td class="font-size-sm">{{ \Carbon\Carbon::parse($task->created_at)->isoFormat('D MMMM Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-primary"
                                        href="{{ url('/tasks/show/').'/'.encrypt($task->id) }}"
                                        data-animation="true" data-toggle="tooltip"
                                        title="Lihat Detail Tugas" data-original-title="Lihat Detail Tugas"
                                        style="margin-right: 3px"
                                    >
                                        <i class="fa fa-fw fa-eye"></i>
                                    </a>
                                    <a class="btn btn-sm btn-warning"
                                        href="{{ url('/tasks/edit/').'/'.encrypt($task->id) }}"
                                        data-animation="true" data-toggle="tooltip"
                                        title="Ubah Tugas" data-original-title="Ubah Tugas"
                                        style="margin-right: 3px"
                                    >
                                        <i class="fa fa-fw fa-pencil-alt"></i>
                                    </a>
                                    @if($task->created_by === $user->id)
                                        <a class="btn btn-sm btn-danger js-swal-confirm"
                                            href="{{ route('tasks.destroy', encrypt($task->id)) }}"
                                            data-toggle="tooltip" title="" data-original-title="Hapus Tugas">
                                            <i class="fa fa-fw fa-times"></i>
                                        </a>
                                    @endif
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
