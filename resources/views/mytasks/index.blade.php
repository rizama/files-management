@extends('layouts.app')

@section('title')
    Tugas Saya - {{ env('APP_NAME') }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css') }}">
@endsection

@section('css_custom')
    <style>
        .empty-data + .dataTables_wrapper {
            display: none;
        }

        .table tr td {
            display: grid;
            padding-left: 0;
            padding-right: 0;
        }

    </style>
@endsection

@section('page-title')
    Tugas Saya
@endsection

@section('content')
    <div class="row">
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)" data-toggle="appear" data-class="animated flipInX">
                <div class="block-content block-content-full">
                    <div class="font-size-h2 text-primary">{{ $task_total }}</div>
                </div>
                <div class="block-content py-2 bg-body-light">
                    <p class="font-w600 font-size-sm text-muted mb-0">
                        Total Tugas
                    </p>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)" data-toggle="appear" data-class="animated flipInX">
                <div class="block-content block-content-full">
                    <div class="font-size-h2 text-success">{{ $task_finished }}</div>
                </div>
                <div class="block-content py-2 bg-body-light">
                    <p class="font-w600 font-size-sm text-muted mb-0">
                        Tugas Selesai
                    </p>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)" data-toggle="appear" data-class="animated flipInX">
                <div class="block-content block-content-full">
                    <div class="font-size-h2 text-warning">{{ $task_progress }}</div>
                </div>
                <div class="block-content py-2 bg-body-light">
                    <p class="font-w600 font-size-sm text-muted mb-0">
                        Tugas Sedang Dikerjakan
                    </p>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)" data-toggle="appear" data-class="animated flipInX">
                <div class="block-content block-content-full">
                    <div class="font-size-h2 text-dark">{{ $task_unprogress }}</div>
                </div>
                <div class="block-content py-2 bg-body-light">
                    <p class="font-w600 font-size-sm text-muted mb-0">
                        Tugas Belum Dikerjakan
                    </p>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)" data-toggle="appear" data-class="animated flipInX">
                <div class="block-content block-content-full">
                    <div class="font-size-h2 text-primary">{{ $doc_total }}</div>
                </div>
                <div class="block-content py-2 bg-body-light">
                    <p class="font-w600 font-size-sm text-muted mb-0">
                        Total Dokumen
                    </p>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)" data-toggle="appear" data-class="animated flipInX">
                <div class="block-content block-content-full">
                    <div class="font-size-h2 text-success">{{ $doc_finished }}</div>
                </div>
                <div class="block-content py-2 bg-body-light">
                    <p class="font-w600 font-size-sm text-muted mb-0">
                        Dokumen Disetujui
                    </p>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)" data-toggle="appear" data-class="animated flipInX">
                <div class="block-content block-content-full">
                    <div class="font-size-h2 text-danger">{{ $doc_reject }}</div>
                </div>
                <div class="block-content py-2 bg-body-light">
                    <p class="font-w600 font-size-sm text-muted mb-0">
                        Dokumen Ditolak
                    </p>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)" data-toggle="appear" data-class="animated flipInX">
                <div class="block-content block-content-full">
                    <div class="font-size-h2 text-dark">{{ $doc_progress }}</div>
                </div>
                <div class="block-content py-2 bg-body-light">
                    <p class="font-w600 font-size-sm text-muted mb-0">
                        Dokumen Menunggu
                    </p>
                </div>
            </a>
        </div>
    </div>
    <div class="row mb-2">
        <table class="table mytask-card-dataTable {{ $user->responsible_tasks == [] ? 'd-none' : '' }}">
            <thead class="d-none">
                <tr>
                    <th class="disable-sorting"></th>
                </tr>
            </thead>
            <tbody class="row">
                @forelse ($user->responsible_tasks as $task)
                    @php
                        $dueDateClass = '';
                        $uploader = [];
                        foreach ($task->files as $file) {
                            $uploader[] = $file->created_by;
                        }

                        if ($task->status == 3){
                            $color = 'success';
                            $status= 'Selesai';
                            $dueDateClass = 'bg-success-light';
                        } elseif (count($task->files) > 0) {
                            if (!in_array(Auth::id(), $uploader)) {
                                $color = 'secondary';
                                $status= 'Belum Dikerjakan';
                            } else {
                                $color = 'warning';
                                $status= 'Sedang Dikerjakan';
                                $dueDateClass = 'bg-warning-light';
                            }
                        } else {
                            $color = 'secondary';
                            $status= 'Belum Dikerjakan';
                        }
                        
                        if ($task->status != 3 && $task->due_date && \Carbon\Carbon::parse($task->due_date) < \Carbon\Carbon::now()){
                            $dueDateClass = 'bg-danger-light';
                        } else {
                            $dueDateClass = $dueDateClass;
                        }
                    @endphp
                    <tr class="col-sm-6 col-md-4 col-lg-4 col-xl-3">
                        <td data-toggle="appear">
                            {{-- <div class="col-12"> --}}
                                <div class="block block-rounded mb-0">
                                    <div class="block-header block-header-default {{ $dueDateClass }}">
                                        <h3 class="block-title">
                                            <p class="clamp-1 mb-0 text-black break-all" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $task->name }}">{{ $task->name }}</p>
                                            <small class="d-block text-black clamp-1 break-all">Kategori: {{ $task->category['name'] ?? '-' }}</small>
                                            <span class="badge badge-{{ $color }}">{{ $status }}</span>
                                            @if ($dueDateClass == 'bg-danger-light')
                                                <span class="badge badge-danger" title="Terlambat" data-toggle="tooltip"><i class="fa fa-hourglass-end"></i></span>
                                                
                                            @endif
                                            <small class="d-block text-black {{ $dueDateClass == 'bg-danger-light' ? 'text-bold' : '' }}">Batas Waktu:</br>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->isoFormat('D MMMM Y, HH:mm')." WIB" : '-' }}</small>
                                        </h3>
                                    </div>
                                    <div class="block-content">
                                        <h4 class="font-w300 block-title">Deskripsi</h4>
                                        <p class="clamp-2 mb-2" style="height: 50px;" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $task->description }}">{{ $task->description == "" ? '-' : $task->description }}</p>
                                        <a class="btn btn-sm bg-info-light text-info btn-block mb-2" href="{{ url('/tasks/show/').'/'.encrypt($task->id) }}" style="white-space: nowrap;">Lihat tugas <i class="fa fa-arrow-right ml-1"></i> </a>
                                    </div>
                                </div>
                            {{-- </div> --}}
                        </td>
                    </tr>
                @empty
                    <div class="col-md empty-data">
                        <p class="p-2 bg-primary-light text-white text-center"><strong>Saat ini tidak ada tugas yang dibebankan kepada anda</strong></p>
                    </div>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection


@section('js_after')
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
@endsection