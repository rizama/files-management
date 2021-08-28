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
                        <th class="disable-sorting text-center">Status<br/>Keterlambatan</th>
                        <th class="disable-sorting text-center" style="width: 110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                            {{-- <td class="text-center font-size-sm">{{ $loop->index + 1 }}</td> --}}
                            <td class="font-w600 font-size-sm">
                                Mochamad
                            </td>
                            <td>
                                DOkumen .txt
                            </td>
                            <td data-order="{{strtotime(\Carbon\Carbon::now())}}">
                                {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, HH mm') }}
                            </td>
                            <td>
                                Tugas 1
                            </td>
                            <td> <span class="badge badge-danger">Terlambat</span> <span class="badge badge-success">Tepat Waktu</span> - </td>
                            <td style="text-align: center;">
                                <div class="btn-group">
                                    <a
                                        class="btn btn-sm btn-success approve-file js-swal-confirm-with-form"
                                        data-type_button="approve"
                                        href="javascript:void(0)"
                                        {{-- href="{{ route('tasks.approve', encrypt($file->id)) }}" --}}
                                        data-title="Apakah anda yakin untuk menyetujui dokumen ini ?"
                                        {{-- data-caption="{{$file->original_name ?? ''}}.{{ App\Http\Controllers\TaskController::mime2ext($file->mime_type) }} {{$file->description ? ', Deskripsi: '.$file->description : ''}}" --}}
                                        data-form_id="verification"
                                        data-success_text="Dokumen Berhasil Disetujui"
                                        data-animation="true" data-toggle="tooltip"
                                        title="Setujui Dokumen"
                                    ><i class="fa fa-fw fa-check"></i></a>
                                    <a
                                        class="btn btn-sm btn-danger reject-file js-swal-confirm-with-form"
                                        data-type_button="reject"
                                        href="javascript:void(0)"
                                        {{-- href="{{ route('tasks.reject', encrypt($file->id)) }}" --}}
                                        data-title="Apakah anda yakin untuk menolak dokumen ini ?"
                                        {{-- data-caption="{{$file->original_name ?? ''}}.{{ App\Http\Controllers\TaskController::mime2ext($file->mime_type) }}{{$file->description ? ', Deskripsi: '.$file->description : ''}}" --}}
                                        data-form_id="verification"
                                        data-success_text="Dokumen Berhasil Ditolak"
                                        data-animation="true" data-toggle="tooltip"
                                        title="Tolak Dokumen"
                                        style="margin-left: 3px"
                                    ><i class="fa fa-fw fa-times"></i></a>
                                    <a class="btn btn-sm btn-primary"
                                        href="javascript:void(0)"
                                        {{-- href="{{ url('/tasks/show/').'/'.encrypt($task->id) }}" --}}
                                        data-animation="true" data-toggle="tooltip"
                                        title="Lihat Detail Tugas" data-original-title="Lihat Detail Tugas"
                                        style="margin-left: 10px"
                                    >
                                        <i class="fa fa-fw fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- END Dynamic Table Full -->

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
                        <th class="defaultSort text-center" style="width: 205px">Tanggal</th>
                        <th class="text-center" style="width: 205px">Batas Waktu</th>
                        <th class="disable-sorting text-center">Status<br/>Keterlambatan</th>
                        <th class="disable-sorting text-center" style="width: 50px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                            {{-- <td class="text-center font-size-sm">{{ $loop->index + 1 }}</td> --}}
                            <td class="font-w600 font-size-sm">
                                Tugas 1
                            </td>
                            <td class="font-w600 font-size-sm">
                                Arif
                            </td>
                            <td data-order="{{strtotime(\Carbon\Carbon::now())}}">
                                {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, HH mm') }}
                            </td>
                            <td data-order="{{strtotime(\Carbon\Carbon::now())}}">
                                {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y, HH mm') }}
                            </td>
                            <td> <span class="badge badge-danger">Terlambat</span> <span class="badge badge-success">Tepat Waktu</span> - </td>
                            <td style="text-align: center;">
                                <a class="btn btn-sm btn-primary"
                                    href="javascript:void(0)"
                                    {{-- href="{{ url('/tasks/show/').'/'.encrypt($task->id) }}" --}}
                                    data-animation="true" data-toggle="tooltip"
                                    title="Lihat Detail Tugas" data-original-title="Lihat Detail Tugas"
                                    style="margin-left: 10px"
                                >
                                    <i class="fa fa-fw fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- END Dynamic Table Full -->

    <form action="" method="POST" id="verification"><form>

@endsection

@section('js_after')
    <script src="{{ asset('js/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables/buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/pages/tables_datatables.js') }}"></script>
@endsection
