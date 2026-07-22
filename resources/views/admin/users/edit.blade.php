@extends('layouts.app')
@section('title', 'Edit Akun')

@section('content')
<div class="card" style="max-width: 600px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('admin.users._form', ['user' => $user])
            <button type="submit" class="btn btn-primary mt-2">Perbarui Akun</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-light mt-2">Batal</a>
        </form>
    </div>
</div>
@endsection
