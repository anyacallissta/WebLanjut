<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangModel;
use Illuminate\Support\Facades\DB;
use App\DataTables\BarangDataTable;

class BarangController extends Controller
{
    public function index(BarangDataTable $dataTable) {
        return $dataTable->render('barang.index');
    }

    public function create() {
        return view('barang.create');
    }

    public function store(Request $request) {
        // Validasi input
        $request->validate([
            'kategoriId' => 'required|integer',
            'kodeBarang' => 'required|string|max:10|unique:barang,barang_kode',
            'namaBarang' => 'required|string|max:100',
            'hargaBeli' => 'required|numeric|min:0',
            'hargaJual' => 'required|numeric|min:0',
        ]);

        // Simpan ke database
        BarangModel::create([
            'kategori_id' => $request->kategoriId,
            'barang_kode' => $request->kodeBarang,
            'barang_nama' => $request->namaBarang,
            'harga_beli' => $request->hargaBeli,
            'harga_jual' => $request->hargaJual,
        ]);

        return redirect('/barang');
    }

    public function edit($id)
    {
        // Ambil data barang berdasarkan ID
        $barang = BarangModel::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'barang_kode' => 'required|string|max:10|unique:barang,barang_kode,' . $id,
            'barang_nama' => 'required|string|max:100',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        // Ambil data barang
        $barang = BarangModel::findOrFail($id);

        // Update data barang
        $barang->update([
            'barang_kode' => $request->barang_kode,
            'barang_nama' => $request->barang_nama,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
        ]);

        return redirect('/barang');
    }

    public function destroy($id)
    {
        $barang = BarangModel::findOrFail($id);
        $barang->delete();

        return redirect('/barang');
    }
}
