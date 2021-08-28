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
                        if ($task->status != 3 && $task->due_date && \Carbon\Carbon::parse($task->due_date) < \Carbon\Carbon::now()){
                            $dueDateClass = 'bg-alt-danger';
                        } else {
                            $dueDateClass = '';
                        }

                        $uploader = [];
                        foreach ($task->files as $file) {
                            $uploader[] = $file->created_by;
                        }

                        if ($task->status == 3){
                            $color = 'success';
                            $status= 'Selesai';
                        } elseif (count($task->files) > 0) {
                            if (!in_array(Auth::id(), $uploader)) {
                                $color = 'secondary';
                                $status= 'Belum Dikerjakan';
                            } else {
                                $color = 'warning';
                                $status= 'Sedang Dikerjakan';
                            }
                        } else {
                            $color = 'secondary';
                            $status= 'Belum Dikerjakan';
                        }
                    @endphp
                    <tr class="col-sm-6 col-md-4 col-lg-4 col-xl-3">
                        <td>
                            {{-- <div class="col-12"> --}}
                                <div class="block block-rounded mb-0">
                                    <div class="block-header block-header-default {{ $dueDateClass }}">
                                        <h3 class="block-title">
                                            <p class="clamp-1 mb-0" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $task->name }}">{{ $task->name }}</p>
                                            <small class="d-block">Kategori: {{ $task->category['name'] ?? '-' }}</small>
                                            <span class="badge badge-{{ $color }}">{{ $status }}</span>
                                            <small class="d-block">Batas Waktu: {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->isoFormat('D MMMM Y, HH mm') : '-' }}</small>
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