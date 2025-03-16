<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangModel;
use Illuminate\Support\Facades\DB;
use App\DataTables\BarangDataTable;

class BarangController extends Controller
{
    public function index(BarangDataTable $dataTable) 
    {
        return $dataTable->render('barang.index');
    }

    public function create() 
    {
        return view('barang.create');
    }

    public function store(Request $request) 
    {
        BarangModel::create([
            'kategori_id' => $request->idKategori,
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
            'kategori_id' => 'required|string|max:10',
            'barang_kode' => 'required|string|max:10',
            'barang_nama' => 'required|string|max:100',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
        ]);

        // Ambil data barang
        $barang = BarangModel::findOrFail($id);

        // Update data barang
        $barang->update([
            'kategori_id' => $request->kategori_id,
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
