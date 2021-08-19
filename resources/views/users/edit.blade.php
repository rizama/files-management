@extends('layouts.app')

@section('title')
    Manajemen Pengguna - {{ env('APP_NAME') }}
@endsection

@section('page-title')
    Manajemen Pengguna
@endsection

@section('child-breadcrumb')
    Ubah Pengguna
@endsection

@section('content')
<div class="block block-rounded">
    <div class="block-header">
        <h3 class="block-title">Ubah Pengguna</h3>
    </div>
    <div class="block-content block-content-full">
        <form action="{{ route('users.update', encrypt($user->id)) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row push">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="name">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Masukan Nama" required value="{{ old('name', $user->name) }}">
                        @error('name')
                            <span style="color: red">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email </label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukan Email" value="{{ old('email', $user->email) }}">
                        @error('email')
                            <span style="color: red">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" placeholder="Masukan Username" autocomplete="off" value="{{ old('username', $user->username) }}">
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
                        <select class="form-control" id="role_id" name="role_id" value={{ $user->role_id }}>
                            @foreach ($roles as $key => $role)
                                <option value="{{ $role->id }}" {{ $role->id == $user->role_id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password">Kata Sandi <span class="text-danger">*</span></label></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Kosongkan jika tidak ingin diubah">
                        @error('password')
                            <span style="color: red">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-right">
                    <button type="submit" class="btn btn-primary" data-toggle="click-ripple">Simpan</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
