@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
@endsection

@section('content')
@if (session()->has('flash_notification.success'))
<div class="alert alert-success alert-dismissable" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <p class="mb-0">{{session('flash_notification.success')}}</p>
</div>
@endif

@section('page-title')
    Beranda
@endsection
<div class="row">
    <div class="col-6 col-lg-3">
        <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
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
        <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
            <div class="block-content block-content-full">
                <div class="font-size-h2 text-success">{{ $task_done }}</div>
            </div>
            <div class="block-content py-2 bg-body-light">
                <p class="font-w600 font-size-sm text-muted mb-0">
                    Tugas Selesai
                </p>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
            <div class="block-content block-content-full">
                <div class="font-size-h2 text-warning">{{ $task_progress }}</div>
            </div>
            <div class="block-content py-2 bg-body-light">
                <p class="font-w600 font-size-sm text-muted mb-0">
                    Tugas Sedang Diproses
                </p>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
            <div class="block-content block-content-full">
                <div class="font-size-h2 text-dark">{{ $task_waiting }}</div>
            </div>
            <div class="block-content py-2 bg-body-light">
                <p class="font-w600 font-size-sm text-muted mb-0">
                    Dokumen Menunggu
                </p>
            </div>
        </a>
    </div>
</div>

<!-- Table Sections (.js-table-sections class is initialized in Helpers.tableToolsSections()) -->
<div class="row">
    <div class="col-12 col-xxl-6">
        <div class="block block-rounded">
            <div class="block-content">
                <div class="table-responsive">
                    <table class="js-table-sections table table-hover table-vcenter">
                        <thead>
                            <tr>
                                <th style="width: 30px; border-top-width: 0;" rowspan="2"></th>
                                <th rowspan="2" style="vertical-align: middle; border-top-width: 0;">Nama</th>
                                <th colspan="5" class="text-center" style="border-top-width: 0;">Status Dokumen</th>
                            </tr>
                            <tr>
                                <th style="width: 15%;" class="text-center">Disetujui</th>
                                <th style="width: 15%;" class="text-center">Menunggu</th>
                                <th style="width: 15%;" class="text-center">Ditolak</th>
                                <th style="width: 15%;" class="text-center">Total</th>
                            </tr>
                        </thead>
            
                        @foreach ($users as $user)
                        <tbody class="js-table-sections-header">
                            <tr>
                                <td class="text-center">
                                    <i class="fa fa-angle-right text-muted"></i>
                                </td>
                                <td class="font-w600 font-size-sm">
                                    <div class="py-1">
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td class="text-center" style="font-size: 1.5em;">
                                    <span class="badge badge-pill badge-success">{{ count($user->files->where('status_approve', 3)) }}</span>
                                </td>
                                <td class="text-center" style="font-size: 1.5em;">
                                    <span class="badge badge-pill badge-dark">{{ count($user->files->where('status_approve', 2)) }}</span>
                                </td>
                                <td class="text-center" style="font-size: 1.5em;">
                                    <span class="badge badge-pill badge-danger">{{ count($user->files->where('status_approve', 4)) }}</span>
                                </td>
                                <td class="text-center" style="font-size: 1.5em;">
                                    <span class="badge badge-pill badge-primary">{{ count($user->files) }}</span>
                                </td>
                            </tr>
                        </tbody>
                        <tbody class="font-size-sm">
                            @foreach ($user->files as $file)
                            <tr>
                                @php
                                    if($file->status_approve == 3){
                                        $status = ['success', 'Disetujui'];
                                    } else if ($file->status_approve == 2) {
                                        $status = ['dark', 'Menunggu'];
                                    } else if ($file->status_approve == 4) {
                                        $status = ['danger', 'Ditolak'];
                                    }
                                @endphp
                                <td colspan="6" class="font-w600 font-size-sm"><a href="{{ route('tasks.show', encrypt($file->task->id)) }}"><span class="mr-2 badge badge-{{ $status[0] }}">{{ $status[1] }}</span> {{ $file->task->name }}</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xxl-6">
        <div class="block block-rounded">
            <div class="block-content">
                <div class="table-responsive">
                    <table class="js-table-sections table table-hover table-vcenter">
                        <thead>
                            <tr>
                                <th rowspan="2" style="vertical-align: middle; border-top-width: 0;">Nama</th>
                                <th colspan="5" class="text-center" style="border-top-width: 0;">Status Penugasan</th>
                            </tr>
                            <tr>
                                <th style="width: 15%;" class="text-center">Disetujui</th>
                                <th style="width: 15%;" class="text-center">Diproses</th>
                                <th style="width: 15%;" class="text-center">Total</th>
                            </tr>
                        </thead>
            
                        @foreach ($data_task as $name => $task)
                        <tbody class="js-table-sections-header">
                            <tr>
                                <td class="font-w600 font-size-sm">
                                    <div class="py-1">
                                        <img class="img-avatar img-avatar48 mr-1" src="{{  asset('media/avatars/avatar1.jpg')  }}" alt="">
                                        {{ $name }}
                                    </div>
                                </td>
                                <td class="text-center" style="font-size: 1.5em;">
                                    <span class="badge badge-pill badge-success">{{ $task['finish'] }}</span>
                                </td>
                                <td class="text-center" style="font-size: 1.5em;">
                                    <span class="badge badge-pill badge-warning">{{ $task['progres'] }}</span>
                                </td>
                                <td class="text-center" style="font-size: 1.5em;">
                                    <span class="badge badge-pill badge-primary">{{ $task['total'] }}</span>
                                </td>
                            </tr>
                        </tbody>
                        <tbody class="font-size-sm">
                        </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END Table Sections -->
@endsection

@section('js_after')
<!-- Page JS Helpers (Table Tools helpers) -->
<script>
    jQuery(function () { One.helpers(['table-tools-checkable', 'table-tools-sections']); });
</script>
@endsection