@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', 'Kategori')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Kategori')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <span>Manage Kategori</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ url('kategori/create') }}" class="btn btn-primary">Add Kategori</a>
                </div>
                {!! $dataTable->table() !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
