@extends('layouts.app')

@section('title')
    Manajemen Role - {{ env('APP_NAME') }}
@endsection

@section('page-title')
    Manajemen Role
@endsection

@section('child-breadcrumb')
    Ubah Role
@endsection

@section('content')
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title">Ubah Role</h3>
        </div>
        <div class="block-content block-content-full">
            <form action="{{ route('roles.update', encrypt($role->id)) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row push">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="name">Nama <span class="text-danger">*</span></label></label>
                            <input type="name" class="form-control" id="name" name="name" placeholder="Masukan Nama"
                                required value="{{ old('name', $role->name) }}">
                            @error('name')
                                <span style="color: red">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="description">Deskripsi <span class="text-danger">*</span></label></label>
                            <textarea type="description" class="form-control" id="description" name="description"
                                placeholder="Masukan Emai" required value>{{ old('description', $role->description) }}</textarea>
                            @error('description')
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
