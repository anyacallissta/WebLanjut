<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\KategoriDataTable;

class KategoriController extends Controller
{
    // public function index()
    // {
    //     // langkah 3
    //     // $data = [
    //     //     'kategori_kode' => 'SNK',
    //     //     'kategori_nama' => 'Snack/Makanan Ringan',
    //     //     'created_at' => now()
    //     // ];

    //     // DB::table('m_kategori')->insert($data);
    //     // return 'Insert data baru berhasil';

    //     // langkah 5
    //     // $row = DB::table('m_kategori')
    //     // ->where('kategori_kode', 'SNK')
    //     // ->update(['kategori_nama' => 'Camilan']);

    //     // return 'Update data berhasil. Jumlah data yang diupdate: ' . $row . ' baris';

    //     // langkah 7
    //     // $row = DB::table('m_kategori')
    //     // ->where('kategori_kode', 'SNK')
    //     // ->delete();

    //     // return 'Delete data berhasil. Jumlah data yang dihapus: ' . $row . ' baris';

    //     // langkah 8
    //     $data = DB::table('m_kategori')->get();
    //     return view('kategori', ['data' => $data]);
    // }

    public function index(KategoriDataTable $dataTable) {
        return $dataTable->render('kategori.index');
    }
}