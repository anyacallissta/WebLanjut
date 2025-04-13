<form action="{{ url('/stok/ajax') }}" method="POST" id="form-create">
    @csrf
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Stok Barang (AJAX)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="barang_id">Nama Barang</label>
                    <select name="barang_id" class="form-control" required>
                        <option value="">- Pilih Barang -</option>
                        @foreach($barang as $b)
                            <option value="{{ $b->barang_id }}">{{ $b->barang_nama }}</option>
                        @endforeach
                    </select>
                    <small class="text-danger error-text" id="error-barang_id"></small>
                </div>

                <div class="form-group">
                    <label for="user_id">Nama Penyetok</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">- Pilih User -</option>
                        @foreach($user as $u)
                            <option value="{{ $u->user_id }}">{{ $u->nama }}</option>
                        @endforeach
                    </select>
                    <small class="text-danger error-text" id="error-user_id"></small>
                </div>

                <div class="form-group">
                    <label for="stok_tanggal">Tanggal Stok</label>
                    <input type="datetime-local" name="stok_tanggal" class="form-control" required>
                    <small class="text-danger error-text" id="error-stok_tanggal"></small>
                </div>

                <div class="form-group">
                    <label for="stok_jumlah">Jumlah Stok</label>
                    <input type="number" name="stok_jumlah" class="form-control" min="1" required>
                    <small class="text-danger error-text" id="error-stok_jumlah"></small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $('#form-create').on('submit', function (e) {
        e.preventDefault();
        let form = this;

        $.ajax({
            url: form.action,
            method: form.method,
            data: $(form).serialize(),
            success: function (response) {
                $('.error-text').text('');
                if (response.status) {
                    $('#myModal').modal('hide');
                    Swal.fire('Berhasil!', response.message, 'success');
                    dataStok.ajax.reload(); // reload DataTable
                } else {
                    $.each(response.msgField, function (key, val) {
                        $('#error-' + key).text(val[0]);
                    });
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function () {
                Swal.fire('Error!', 'Terjadi kesalahan pada server', 'error');
            }
        });
    });
</script>