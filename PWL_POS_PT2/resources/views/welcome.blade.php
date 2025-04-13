@extends('layouts.template')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="alert alert-primary shadow-sm rounded-lg d-flex align-items-center">
            <div>
                <h4 class="mb-1">Halo, <strong>{{ Auth::user()->nama }}</strong>!</h4>
                <p class="mb-0">Selamat datang di <strong>Point of Sale</strong>.</p>
            </div>
        </div>
    </div>
</div>

@endsection