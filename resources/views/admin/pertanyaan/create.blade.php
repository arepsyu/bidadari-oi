@extends('layouts.app')
@section('title', 'Tambah Pertanyaan')

@section('content')
<div class="card" style="max-width: 750px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.pertanyaan.store') }}">
            @csrf
            <input type="hidden" name="redirect_query" value="{{ request()->query('redirect_query') }}">
            @include('admin.pertanyaan._form')
            <button type="submit" class="btn btn-primary mt-2">Simpan</button>
            <a href="{{ route('admin.pertanyaan.index') }}?{{ request()->query('redirect_query') }}" class="btn btn-light mt-2">Batal</a>
        </form>
    </div>
</div>
@endsection
