@extends('layouts.app')
@section('title', 'Tambah Jenis Data')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.requirements.store') }}">
            @csrf
            @include('admin.requirements._form')
            <button type="submit" class="btn btn-primary mt-2">Simpan</button>
            <a href="{{ route('admin.requirements.index') }}" class="btn btn-light mt-2">Batal</a>
        </form>
    </div>
</div>
@endsection
