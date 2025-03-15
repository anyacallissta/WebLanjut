@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', 'Barang')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Barang')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <span>Manage Barang</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ url('barang/create') }}" class="btn btn-primary">Add Barang</a>
                </div>
                {!! $dataTable->table() !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush