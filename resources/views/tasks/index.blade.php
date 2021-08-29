@extends('layouts.app')

@section('title')
    Manajemen Tugas - {{ env('APP_NAME') }}
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

    @if (session()->has('task.created'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{ session('task.created') }}</p>
        </div>
    @endif

    @if (session()->has('task.updated'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{ session('task.updated') }}</p>
        </div>
    @endif

    @if (session()->has('task.deleted'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{ session('task.deleted') }}</p>
        </div>
    @endif


    <!-- Dynamic Table Full -->
    <div class="block block-rounded">
        <div class="block-header">
            <h2 class="block-title">Daftar Tugas</h2>
            @if ($user->role->code == 'level_1')
                <a class="btn btn-primary pull-right btn-sm" href="{{ url('/tasks/create') }}">Tambah Tugas</a>
            @endif
        </div>
        <div class="block-content block-content-full">
            <table class="table table-bordered table-vcenter js-dataTable-full">
                <thead>
                    <tr>
                        {{-- <th class="text-center" style="width: 80px;">No</th> --}}
                        <th>Nama Tugas</th>
                        <th>Kategori</th>
                        <th>Staf</th>
                        <th class="defaultSort">Tanggal</th>
                        <th>Batas Waktu</th>
                        <th>Status</th>
                        <th class="disable-sorting text-center" style="width: 110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        @php
                            if ($task->status != 3 && $task->due_date && \Carbon\Carbon::parse($task->due_date) < \Carbon\Carbon::now()){
                                $dueDateClass = 'bg-alt-danger';
                            } else {
                                $dueDateClass = '';
                            }
                        @endphp
                        <tr class="{{ $dueDateClass }}">
                            {{-- <td class="text-center font-size-sm">{{ $loop->index + 1 }}</td> --}}
                            <td class="font-w600 font-size-sm">
                                {{ $task->name }}
                            </td>
                            <td>
                                {{ $task->category->name ?? '-' }}
                            </td>
                            <td class="font-w600 font-size-sm">
                                @if($task->assign_to == 'all')
                                    Semua Staf
                                @elseif( $task->assign_to == null)
                                    -
                                @else
                                    @forelse ($task->responsible_person as $responsible_person)
                                        {{ $responsible_person->name }}{{ $loop->last ? '' : ', ' }}
                                    @empty
                                        -
                                    @endforelse
                                @endif
                            </td>
                            <td class="font-size-sm" data-order="{{strtotime($task->created_at)}}">{{ \Carbon\Carbon::parse($task->created_at)->isoFormat('D MMMM Y, HH:mm') }} WIB</td>
                            <td class="font-size-sm" data-order="{{strtotime($task->due_date)}}">{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->isoFormat('D MMMM Y, HH:mm').' WIB' : '-' }}</td>
                            
                            @php
                                if ($task->status == 3){
                                    $color = 'success';
                                    $status= 'Selesai';
                                } elseif (count($task->files) > 0) {
                                    $color = 'warning';
                                    $status= 'Sedang Dikerjakan';
                                } else {
                                    $color = 'secondary';
                                    $status= 'Belum Dikerjakan';
                                }
                            @endphp
                            <td>
                                <span class="badge badge-{{ $color }}">{{ $status }}</span>
                            </td>
                            <td style="text-align: center;">
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-primary"
                                        href="{{ url('/tasks/show/').'/'.encrypt($task->id) }}"
                                        data-animation="true" data-toggle="tooltip"
                                        title="Lihat Detail Tugas" data-original-title="Lihat Detail Tugas"
                                        style="margin-right: 3px"
                                    >
                                        <i class="fa fa-fw fa-eye"></i>
                                    </a>
                                    @if($task->created_by == $user->id)
                                        <a class="btn btn-sm btn-warning"
                                            href="{{ url('/tasks/edit/').'/'.encrypt($task->id) }}"
                                            data-animation="true" data-toggle="tooltip"
                                            title="Ubah Tugas" data-original-title="Ubah Tugas"
                                            style="margin-right: 3px"
                                        >
                                            <i class="fa fa-fw fa-pencil-alt"></i>
                                        </a>
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
