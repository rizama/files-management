@extends('layouts.app')

@section('title')
    Tugas Saya - {{ env('APP_NAME') }}
@endsection

@section('page-title')
    Tugas Saya
@endsection

@section('content')
    <div class="row">
        @forelse ($user->responsible_tasks as $task)
            <div class="col-sm-6 col-md-4 col-lg-4 col-xl-3">
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">
                            <p class="clamp-1 mb-0" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $task->name }}">{{ $task->name }}</p>
                            <small class="d-block">Kategori: {{ $task->category['name'] ?? '-' }}</small>
                            <span class="badge badge-{{ $task->status_task['code'] == 'progress' ? 'warning' : 'success' }}">{{ $task->status_task['name'] }}</span>
                        </h3>
                    </div>
                    <div class="block-content">
                        <h4 class="font-w300 block-title">Deskripsi</h4>
                        <p class="clamp-2 mb-2" style="height: 50px;" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $task->description }}">{{ $task->description == "" ? '-' : $task->description }}</p>
                        <a class="btn btn-sm bg-info-light text-info btn-block mb-2" href="{{ url('/tasks/show/').'/'.encrypt($task->id) }}" style="white-space: nowrap;">Lihat tugas <i class="fa fa-arrow-right ml-1"></i> </a>
                    </div>
                </div>
            </div>
        @empty
            <p>Saat ini tidak ada tugas yang dibebankan kepada anda</p>
        @endforelse
    </div>
@endsection

