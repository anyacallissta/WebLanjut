@extends('layouts.app')

{{-- Customize layout sections --}}

@section('subtitle', 'Barang')
@section('content_header_title', 'Barang')
@section('content_header_subtitle', 'Create')

{{-- Content body: main page content --}}

@section('content')
    <div class="container">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Tambah Barang baru</h3>
            </div>
            <form method="post" action="../barang">
                <div class="card-body">
                    <div class="form-group">
                        <label for="idKategori">Id Kategori</label>
                        <input type="text" class="form-control" id="idKategori" name="idKategori" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="kodeBarang">Kode Barang</label>
                        <input type="text" class="form-control" id="kodeBarang" name="kodeBarang" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="namaBarang">Nama Barang</label>
                        <input type="text" class="form-control" id="namaBarang" name="namaBarang" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="hargaBeli">Harga Beli</label>
                        <input type="text" class="form-control" id="hargaBeli" name="hargaBeli" placeholder="">
                    </div>
                    <div class="form-group">
                        <label for="hargaJual">Harga Jual</label>
                        <input type="text" class="form-control" id="hargaJual" name="hargaJual" placeholder="">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection