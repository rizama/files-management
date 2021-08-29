@extends('layouts.app')

@section('title')
   Detail Notifikasi - {{ env('APP_NAME') }}
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css') }}">
@endsection

@section('page-title')
   Detail Notifikasi
@endsection

@section('content')
    @if (Auth::user()->role->code == 'level_1')
        <!-- Dynamic Table Full -->
        <div class="block block-rounded">
            <div class="block-header">
                <h2 class="block-title">Daftar Dokumen Menunggu Persetujuan</h2>
            </div>
            <div class="block-content block-content-full">
                <table class="table table-bordered table-vcenter js-dataTable-full">
                    <thead>
                        <tr>
                            <th>Pengunggah</th>
                            <th>Nama Dokumen</th>
                            <th class="defaultSort">Tanggal Unggah</th>
                            <th>Nama Tugas</th>
                            <th class="disable-sorting text-center">Status Waktu</th>
                            <th class="disable-sorting text-center" style="width: 110px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contents as $content)
                        <tr>
                            {{-- <td class="text-center font-size-sm">{{ $loop->index + 1 }}</td> --}}
                            <td class="font-w600 font-size-sm">
                                {{ $content->user->name }}
                            </td>
                            <td>
                                {{ $content->original_name }}
                            </td>
                            <td data-order="{{strtotime(\Carbon\Carbon::now())}}">
                                {{ \Carbon\Carbon::parse($content->created_at)->isoFormat('D MMMM Y, HH mm') }}
                            </td>
                            <td>
                                {{ $content->task->name }}
                            </td>
                            @php
                                if ($content->task->due_date) {
                                    if (\Carbon\Carbon::parse($content->created_at) < \Carbon\Carbon::parse($content->task->due_date)) {
                                        $lateStatus = 'Tepat Waktu';
                                        $lateStatusColor = 'info';
                                    } else {
                                        $lateStatus = 'Terlambat';
                                        $lateStatusColor = 'danger';
                                    }
                                } else {
                                    $lateStatus = '-';
                                    $lateStatusColor = 'secondary';
                                }
                            @endphp
                            <td><span class="badge badge-{{ $lateStatusColor }}">{{ $lateStatus }}</span></td>
                            <td style="text-align: center;">
                                <div class="btn-group">
                                    <a
                                        class="btn btn-sm btn-success approve-file js-swal-confirm-with-form"
                                        data-type_button="approve"
                                        href="{{ route('tasks.approve', encrypt($content->id)) }}"
                                        data-title="Apakah anda yakin untuk menyetujui dokumen ini ?"
                                        data-caption="{{$content->original_name ?? ''}}.{{ App\Http\Controllers\TaskController::mime2ext($content->mime_type) }} {{$content->description ? ', Deskripsi: '.$content->description : ''}}"
                                        data-form_id="verification"
                                        data-success_text="Dokumen Berhasil Disetujui"
                                        data-animation="true" data-toggle="tooltip"
                                        title="Setujui Dokumen"
                                    ><i class="fa fa-fw fa-check"></i></a>
                                    <a
                                        class="btn btn-sm btn-danger reject-file js-swal-confirm-with-form"
                                        data-type_button="reject"
                                        href="{{ route('tasks.reject', encrypt($content->id)) }}"
                                        data-title="Apakah anda yakin untuk menolak dokumen ini ?"
                                        data-caption="{{$content->original_name ?? ''}}.{{ App\Http\Controllers\TaskController::mime2ext($content->mime_type) }}{{$content->description ? ', Deskripsi: '.$content->description : ''}}"
                                        data-form_id="verification"
                                        data-success_text="Dokumen Berhasil Ditolak"
                                        data-animation="true" data-toggle="tooltip"
                                        title="Tolak Dokumen"
                                        style="margin-left: 3px"
                                    ><i class="fa fa-fw fa-times"></i></a>
                                    <a class="btn btn-sm btn-primary"
                                        href="{{ url('/tasks/show/').'/'.encrypt($content->task->id) }}"
                                        data-animation="true" data-toggle="tooltip"
                                        title="Lihat Detail Tugas" data-original-title="Lihat Detail Tugas"
                                        style="margin-left: 10px"
                                    >
                                        <i class="fa fa-fw fa-eye"></i>
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

    @else
        <!-- Dynamic Table Full -->
        <div class="block block-rounded">
            <div class="block-header">
                <h2 class="block-title">Daftar Tugas Belum Selesai</h2>
            </div>
            <div class="block-content block-content-full">
                <table class="table table-bordered table-vcenter js-dataTable-full">
                    <thead>
                        <tr>
                            <th>Nama Tugas</th>
                            <th>Staf</th>
                            <th class="defaultSort text-center" style="width: 220px">Tanggal</th>
                            <th class="text-center" style="width: 220px">Batas Waktu</th>
                            <th class="disable-sorting text-center" style="width: 50px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contents as $content)
                            @php
                                if ($content->status != 3 && $content->due_date && \Carbon\Carbon::parse($content->due_date) < \Carbon\Carbon::now()){
                                    $dueDateClass = 'bg-danger-light';
                                } else {
                                    $dueDateClass = '';
                                }
                            @endphp
                            <tr class="{{ $dueDateClass }}">
                                <td class="font-w600 font-size-sm">
                                    {{ $content->name }}
                                </td>
                                <td class="font-w600 font-size-sm">
                                    @if($content->assign_to == 'all')
                                        Semua Staf
                                    @elseif( $content->assign_to == null)
                                        -
                                    @else
                                        @forelse ($content->responsible_person as $responsible_person)
                                            {{ $responsible_person->name }}{{ $loop->last ? '' : ', ' }}
                                        @empty
                                            -
                                        @endforelse
                                    @endif
                                </td>
                                <td class="font-size-sm" data-order="{{strtotime($content->created_at)}}">{{ \Carbon\Carbon::parse($content->created_at)->isoFormat('D MMMM Y, HH:mm') }} WIB</td>
                                <td class="font-size-sm" data-order="{{strtotime($content->due_date)}}">{{ $content->due_date ? \Carbon\Carbon::parse($content->due_date)->isoFormat('D MMMM Y, HH:mm').' WIB' : '-' }}</td>
                                <td style="text-align: center;">
                                    <a class="btn btn-sm btn-primary"
                                        href="{{ url('/tasks/show/').'/'.encrypt($content->id) }}"
                                        data-animation="true" data-toggle="tooltip"
                                        title="Lihat Detail Tugas" data-original-title="Lihat Detail Tugas"
                                    >
                                        <i class="fa fa-fw fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END Dynamic Table Full -->

    @endif
    <form action="" method="POST" id="verification"><form>

@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
@endsection
