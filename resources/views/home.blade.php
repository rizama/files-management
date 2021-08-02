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
        <a class="block block-rounded block-link-shadow text-center" href="be_pages_ecom_orders.html">
            <div class="block-content block-content-full">
                <div class="font-size-h2 text-primary">35</div>
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
                <div class="font-size-h2 text-success">20</div>
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
                <div class="font-size-h2 text-dark">10</div>
            </div>
            <div class="block-content py-2 bg-body-light">
                <p class="font-w600 font-size-sm text-muted mb-0">
                    Tugas Menunggu Persetujuan
                </p>
            </div>
        </a>
    </div>
    <div class="col-6 col-lg-3">
        <a class="block block-rounded block-link-shadow text-center" href="javascript:void(0)">
            <div class="block-content block-content-full">
                <div class="font-size-h2 text-warning">5</div>
            </div>
            <div class="block-content py-2 bg-body-light">
                <p class="font-w600 font-size-sm text-muted mb-0">
                    Tugas Sedang Diproses
                </p>
            </div>
        </a>
    </div>
</div>

<!-- Table Sections (.js-table-sections class is initialized in Helpers.tableToolsSections()) -->
<div class="block block-rounded">
    <div class="block-header">
        <h3 class="block-title">Status Proges Pekerjaan</h3>
    </div>
    <div class="block-content">
        <table class="js-table-sections table table-hover table-vcenter">
            <thead>
                <tr>
                    <th style="width: 30px;"></th>
                    <th>Nama</th>
                    <th style="width: 15%;">Total</th>
                    <th style="width: 15%;">Selesai</th>
                    <th style="width: 15%;">Menunggu</th>
                    <th style="width: 15%;">Dikerjakan</th>
                </tr>
            </thead>
            <tbody class="js-table-sections-header show table-active">
                <tr>
                    <td class="text-center">
                        <i class="fa fa-angle-right text-muted"></i>
                    </td>
                    <td class="font-w600 font-size-sm">
                        <div class="py-1">
                            Septian
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-primary">20</span>
                    </td>
                    <td>
                        <span class="badge badge-success">10</span>
                    </td>
                    <td>
                        <span class="badge badge-dark">5</span>
                    </td>
                    <td>
                        <span class="badge badge-warning">5</span>
                    </td>
                </tr>
            </tbody>
            <tbody class="font-size-sm">
                <tr>
                    <td class="text-center"><span class="badge badge-warning">Dikerjakan</span></td>
                    <td colspan="5" class="font-w600 font-size-sm">Perencanaan Bulan Agustus</td>
                </tr>
                <tr>
                    <td class="text-center"><span class="badge badge-success">Selesai</span></td>
                    <td colspan="5" class="font-w600 font-size-sm">Perencanaan Bulan Juli</td>
                </tr>
            </tbody>
                        <tbody class="js-table-sections-header">
                <tr>
                    <td class="text-center">
                        <i class="fa fa-angle-right text-muted"></i>
                    </td>
                    <td class="font-w600 font-size-sm">
                        <div class="py-1">
                            Dwi Cahyo
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-primary">15</span>
                    </td>
                    <td>
                        <span class="badge badge-success">8</span>
                    </td>
                    <td>
                        <span class="badge badge-dark">2</span>
                    </td>
                    <td>
                        <span class="badge badge-warning">5</span>
                    </td>
                </tr>
            </tbody>
            <tbody class="font-size-sm">
                <tr>
                    <td class="text-center"></td>
                    <td colspan="5" class="font-w600 font-size-sm">Perencanaan Bulan Agustus</td>
                </tr>
                <tr>
                    <td class="text-center"></td>
                    <td colspan="5" class="font-w600 font-size-sm">Perencanaan Bulan Juli</td>
                </tr>
            </tbody>
        </table>
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