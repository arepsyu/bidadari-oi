@extends('layouts.app')
@section('title', 'Tambah Akun')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users._form')
            <button type="submit" class="btn btn-primary mt-2">Simpan Akun</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-light mt-2">Batal</a>
        </form>
    </div>
</div>
@endsection
