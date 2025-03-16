@extends('layouts.app')

{{-- Customize layout sections --}}
@section('subtitle', 'Level')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Level')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <span>Manage Level</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-end mb-2">
                    <a href="{{ url('level/create') }}" class="btn btn-primary">Add Level</a>
                </div>
                {!! $dataTable->table() !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
