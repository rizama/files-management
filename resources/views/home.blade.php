@extends('layouts.app')

@section('content')
    @if (session()->has('flash_notification.success'))
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <p class="mb-0">{{session('flash_notification.success')}}</p>
        </div>
    @endif

    <div class="block block-rounded">
        <div class="block-header block-header-default">
            <h3 class="block-title">Recent File</h3>
            <div class="block-options">
                <button type="button" class="btn btn-sm btn-alt-primary" data-toggle="class-toggle"
                    data-target="#one-dashboard-search-orders" data-class="d-none">
                    <i class="fa fa-search"></i>
                </button>
                <div class="dropdown d-inline-block">
                    <button type="button" class="btn btn-sm btn-alt-primary" id="dropdown-recent-orders-filters"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-fw fa-flask"></i>
                        Filters
                        <i class="fa fa-angle-down ml-1"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-md dropdown-menu-right font-size-sm"
                        aria-labelledby="dropdown-recent-orders-filters">
                        <a class="dropdown-item font-w500 d-flex align-items-center justify-content-between"
                            href="javascript:void(0)">
                            Pending..
                            <span class="badge badge-primary badge-pill">35</span>
                        </a>
                        <a class="dropdown-item font-w500 d-flex align-items-center justify-content-between"
                            href="javascript:void(0)">
                            Processing
                            <span class="badge badge-primary badge-pill">15</span>
                        </a>
                        <a class="dropdown-item font-w500 d-flex align-items-center justify-content-between"
                            href="javascript:void(0)">
                            For Delivery
                            <span class="badge badge-primary badge-pill">20</span>
                        </a>
                        <a class="dropdown-item font-w500 d-flex align-items-center justify-content-between"
                            href="javascript:void(0)">
                            Canceled
                            <span class="badge badge-primary badge-pill">72</span>
                        </a>
                        <a class="dropdown-item font-w500 d-flex align-items-center justify-content-between"
                            href="javascript:void(0)">
                            Delivered
                            <span class="badge badge-primary badge-pill">890</span>
                        </a>
                        <a class="dropdown-item font-w500 d-flex align-items-center justify-content-between"
                            href="javascript:void(0)">
                            All
                            <span class="badge badge-primary badge-pill">997</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div id="one-dashboard-search-orders" class="block-content border-bottom d-none">
            <!-- Search Form -->
            <form action="be_pages_dashboard.html" method="POST" onsubmit="return false;">
                <div class="form-group push">
                    <div class="input-group">
                        <input type="text" class="form-control" id="one-ecom-orders-search" name="one-ecom-orders-search"
                            placeholder="Search recent orders..">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fa fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </form>
            <!-- END Search Form -->
        </div>
        <div class="block-content">
            <!-- Recent Orders Table -->
            <div class="table-responsive">
                <table class="table table-borderless table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 120px;">Task ID</th>
                            <th>Created</th>
                            <th>Uploader</th>
                            <th>Status</th>
                            <th>File Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center font-size-sm">
                                <a class="font-w600" href="javascript:void(0)">
                                    <strong>T.0001</strong>
                                </a>
                            </td>
                            <td class="font-size-sm font-w600 text-muted">11 min ago</td>
                            <td class="font-size-sm">
                                <a class="font-w600" href="javascript:void(0)">Rizky Purnama</a>
                            </td>
                            <td>
                                <span class="font-size-sm font-w600 px-2 py-1 rounded">Waiting Approval</span>
                            </td>
                            <td class="d-none d-sm-table-cell font-size-sm font-w600 text-muted">File.txt</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- END Recent Orders Table -->

            <!-- Pagination -->
            <nav aria-label="Photos Search Navigation">
                <ul class="pagination pagination-sm justify-content-end mt-2">
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)" tabindex="-1" aria-label="Previous">
                            Prev
                        </a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="javascript:void(0)">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)">4</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)" aria-label="Next">
                            Next
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- END Pagination -->
        </div>
    </div>

    <form action="be_pages_generic_search.html" method="POST" class="mb-2">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search..">
            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="fa fa-fw fa-search"></i>
                </span>
            </div>
        </div>
    </form>
    <!-- Results -->
    <div class="block block-rounded overflow-hidden">
        <ul class="nav nav-tabs nav-tabs-block" data-toggle="tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#search-projects">Projects</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#search-users">Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#search-photos">Photos</a>
            </li>
        </ul>
        <div class="block-content tab-content overflow-hidden">
            <!-- Projects -->
            <div class="tab-pane fade fade-up show active" id="search-projects" role="tabpanel">
                <div class="font-size-h4 font-w600 p-2 mb-4 border-left border-4x border-primary bg-body-light">
                    <span class="text-primary font-w700">6</span> projects found for <mark class="text-danger">HTML</mark>
                </div>
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th style="width: 50%;">Project</th>
                            <th class="d-none d-lg-table-cell text-center" style="width: 15%;">Status</th>
                            <th class="d-none d-lg-table-cell text-center" style="width: 15%;">Sales</th>
                            <th class="text-center" style="width: 20%;">Earnings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <h4 class="h5 mt-3 mb-2">
                                    <a href="javascript:void(0)">Web Application Framework</a>
                                </h4>
                                <p class="d-none d-sm-block text-muted">
                                    Maecenas ultrices, justo vel imperdiet gravida, urna ligula hendrerit nibh, ac cursus nibh sapien in purus. Mauris tincidunt tincidunt turpis in porta.
                                </p>
                            </td>
                            <td class="d-none d-lg-table-cell text-center">
                                <span class="badge badge-success">Completed</span>
                            </td>
                            <td class="d-none d-lg-table-cell font-size-xl text-center font-w600">1603</td>
                            <td class="font-size-xl text-center font-w600">$ 35,287</td>
                        </tr>
                    </tbody>
                </table>
                <nav aria-label="Projects Search Navigation">
                    <ul class="pagination pagination-sm">
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" tabindex="-1" aria-label="Previous">
                                Prev
                            </a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="javascript:void(0)">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)">4</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" aria-label="Next">
                                Next
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <!-- END Projects -->

            <!-- Users -->
            <div class="tab-pane fade fade-up" id="search-users" role="tabpanel">
                <div class="font-size-h4 font-w600 p-2 mb-4 border-left border-4x border-primary bg-body-light">
                    <span class="text-primary font-w700">192</span> results found for <mark class="text-danger">client</mark>
                </div>
                <table class="table table-striped table-vcenter">
                    <thead>
                        <tr>
                            <th class="d-none d-sm-table-cell text-center" style="width: 40px;">#</th>
                            <th class="text-center" style="width: 70px;"><i class="si si-user"></i></th>
                            <th>Name</th>
                            <th class="d-none d-sm-table-cell">Email</th>
                            <th class="d-none d-lg-table-cell" style="width: 15%;">Access</th>
                            <th class="text-center" style="width: 80px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="d-none d-sm-table-cell text-center">
                                <span class="badge badge-pill badge-primary">10</span>
                            </td>
                            <td class="text-center">
                                <img class="img-avatar img-avatar48" src="{{ asset('oneui/src/assets/media/avatars/avatar16.jpg') }}" alt="">
                            </td>
                            <td class="font-w600">
                                <a href="javascript:void(0)">Jeffrey Shaw</a>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                client10@example.com
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <span class="badge badge-success">VIP</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-secondary" data-toggle="tooltip" title="Edit Client">
                                        <i class="fa fa-pencil-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary" data-toggle="tooltip" title="Delete Client">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <nav aria-label="Users Search Navigation">
                    <ul class="pagination pagination-sm">
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" tabindex="-1" aria-label="Previous">
                                Prev
                            </a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="javascript:void(0)">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)">4</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" aria-label="Next">
                                Next
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <!-- END Users -->

            <!-- Photos -->
            <div class="tab-pane fade fade-up" id="search-photos" role="tabpanel">
                <div class="font-size-h4 font-w600 p-2 mb-4 border-left border-4x border-primary bg-body-light">
                    <span class="text-primary font-w700">85</span> photos found for <mark class="text-danger">wallpaper</mark>
                </div>
                <div class="row gutters-tiny push">
                    <div class="col-md-6 col-lg-4 col-xl-3 push">
                        <img class="img-fluid" src="{{ asset('oneui/src/assets/media/photos/photo1.jpg') }}" alt="">
                    </div>
                    <div class="col-md-6 col-lg-4 col-xl-3 push">
                        <img class="img-fluid" src="{{ asset('oneui/src/assets/media/photos/photo2.jpg') }}" alt="">
                    </div>
                </div>
                <nav aria-label="Photos Search Navigation">
                    <ul class="pagination pagination-sm">
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" tabindex="-1" aria-label="Previous">
                                Prev
                            </a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="javascript:void(0)">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)">4</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" aria-label="Next">
                                Next
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <!-- END Photos -->
        </div>
    </div>
    <!-- END Results -->
@endsection
