@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit User</h2>
    <form action="{{ route('user.update', $user->user_id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="level_id" class="form-label">ID Level</label>
            <input type="text" class="form-control" id="level_id" name="level_id" value="{{ $user->level_id }}" required>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="{{ $user->username }}" required>
        </div>

        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ $user->nama }}" required>
        </div>

        <div class="mb-3">
            <label for="nama" class="form-label">Password</label>
            <input type="text" class="form-control" id="password" name="password" value="{{ $user->password }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ url('/users') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection