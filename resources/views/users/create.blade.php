@extends('layouts.app')

@section('title')
    Manajemen Pengguna - {{ env('APP_NAME') }}
@endsection

@section('page-title')
    Manajemen Pengguna
@endsection

@section('child-breadcrumb')
    Buat Pengguna
@endsection

@section('content')
<div class="block block-rounded">
    <div class="block-header">
        <h3 class="block-title">Tambah Pengguna</h3>
    </div>
    <div class="block-content block-content-full">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="row push">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="name">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Masukan Nama" required value="{{ old('name') }}">
                        @error('name')
                            <span style="color: red">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="(Optional) Masukan Email / Kosongkan jika tidak perlu" autocomplete="off" value="{{ old('email') }}">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="username">Username <span class="text-danger">*</span></label></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" placeholder="Masukan Username" autocomplete="off" value="{{ old('username') }}">
                        @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="role_id">Pilih Role <span class="text-danger">*</span></label></label>
                        <select class="form-control" id="role_id" name="role_id" required>
                            <option value="" disabled selected>Pilih Role</option>
                            @foreach ($roles as $key => $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password">Kata Sandi <span class="text-danger">*</span></label></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Masukan Kata Sandi" required autocomplete="off">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-primary" data-toggle="click-ripple">Buat</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
