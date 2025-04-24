@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Stok</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-info">Import Stok</button>
                <a href="{{ url('/stok/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Stok (Excel)</a>
                <a href="{{ url('/stok/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Stok (PDF)</a>
                <button onclick="modalAction('{{ url('/stok/create_ajax') }}')" class="btn btn-success">Tambah Data Ajax</button>
            </div>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-sm row text-sm mb-0">
                            <label class="col-md-1 col-form-label">Filter</label>
                            <div class="col-md-3">
                                <select name="filter_barang" class="form-control form-control-sm filter_barang">
                                    <option value="">- Semua -</option>
                                        @foreach($barang as $l)
                                            <option value="{{ $l->barang_id }}">{{ $l->barang_nama }}</option>
                                        @endforeach
                                </select>
                                <small class="form-text text-muted">Nama Barang</small>
                            </div>
                    </div>
                </div>
            </div>

            <table class="table table-bordered table-sm table-striped table-hover" id="table_stok">
                <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <!-- <th class="text-center">ID Stok</th> -->
                        <th class="text-center">Nama Barang</th>
                        <th class="text-center">Nama Penyetok</th>
                        <th class="text-center">Tanggal Stok</th>
                        <th class="text-center">Jumlah Stok</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="75%"></div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function () {
                $('#myModal').modal('show');
            });
        }

        var dataStok;

        $(document).ready(function () {
            dataStok = $('#table_stok').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('stok/list') }}",
                    type: "POST",
                    data: function (d) {
                        d.filter_barang = $('.filter_barang').val();
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex",
                        className: "text-center",
                        width: "4%",
                        orderable: false,
                        searchable: false
                    },
                    // {
                    //     data: "stok_id",
                    //     className: "text-center",
                    //     width: "7%",
                    //     orderable: true,
                    //     searchable: false
                    // },
                    {
                        data: "barang.barang_nama",
                        className: "",
                        width: "20%",
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: "user.nama",
                        className: "",
                        width: "30%",
                        orderable: false,
                        searchable: true
                    },
                    {
                        data: "stok_tanggal",
                        className: "text-center",
                        width: "12%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "stok_jumlah",
                        className: "text-center",
                        width: "12%",
                        orderable: true,
                        searchable: false,
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#table_stok_filter input').unbind().bind('keyup', function (e) {
                if (e.keyCode == 13) {
                    dataStok.search(this.value).draw();
                }
            });

            $('.filter_barang').change(function () {
                dataStok.draw();
            });

            $('#barang_id, #user_id').change(function () {
                dataStok.ajax.reload();
            });
        });
    </script>
@endpush