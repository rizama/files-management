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
                        <h3 class="block-title">{{ $task->name }}</h3>
                        <small></small>
                    </div>
                    <div class="block-content">
                        <p>Deskripsi: {{ $task->description }}</p>
                        <p>Kategori : {{ $task->category['name'] }}</p>
                        <p>Status   : {{ $task->status_task['name'] }}</p>
                        <div class="text-right mb-1">
                            <a class="btn btn-sm bg-info-light text-info" href="{{ url('/tasks/show/').'/'.encrypt($task->id) }}">Lihat tugas <i class="fa fa-arrow-right ml-1"></i> </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p>Saat ini tidak ada tugas yang dibebankan kepada anda</p>
        @endforelse
    </div>
@endsection

