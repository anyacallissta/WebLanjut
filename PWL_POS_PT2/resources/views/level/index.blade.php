@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Level</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/level/import') }}')" class="btn btn-info">Import Level</button>
                <a href="{{ url('/level/export_excel') }}" class="btn btn-primary"><i class="fa fa-fileexcel"></i>Export Level (Excel)</a>
                <a href="{{ url('/level/export_pdf') }}" class="btn btn-warning"><i class="fa fa-filepdf"></i> Export Level (PDF)</a>
                <button onclick="modalAction('{{ url('level/create_ajax') }}')" class="btn btn-success">Tambah Data Ajax</button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered table-striped table-hover table-sm" id="table_level">
                <thead>
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">ID Level</th>
                        <th style="text-align: center;">Kode Level</th>
                        <th style="text-align: center;">Nama Level</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('js')
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function () {
            $('#myModal').modal('show');
        });
    }

    var tableLevel;
    $(document).ready(function () {
        tableLevel = $('#table_level').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('level/list') }}",
                type: "POST",
                dataType: "json",
            },
            columns: [
                {
                    data: "DT_RowIndex",
                    className: "text-center",
                    width: "4%",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "level_id",
                    className: "text-center",
                    width: "10%",
                    orderable: true,
                    searchable: false
                },
                {
                    data: "level_kode",
                    className: "text-center",
                    width: "20%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "level_nama",
                    className: "",
                    width: "40%",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "aksi",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                }
            ]
        });
    });
</script>
@endpush