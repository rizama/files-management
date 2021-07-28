@extends('layouts.app')

@section('title')
User Manajemen - SIMANTAP
@endsection

@section('content')
<div class="block block-rounded">
    <div class="block-header">
        <h3 class="block-title">Tambah User</h3>
    </div>
    <div class="block-content block-content-full">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="row push">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="name">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Masukan Nama" required>
                        @error('name')
                            <span style="color: red">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password">Kata Sandi <span class="text-danger">*</span></label></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Masukan Kata Sandi" required>
                        @error('password')
                            <span style="color: red">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="email">Email <span class="text-danger">*</span></label></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukan Emai" required>
                        @error('email')
                            <span style="color: red">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="role_id">Pilih Role <span class="text-danger">*</span></label></label>
                        <select class="form-control" id="role_id" name="role_id">
                            @foreach ($roles as $key => $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-4">
                    <button type="submit" class="btn btn-primary" data-toggle="click-ripple">Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
