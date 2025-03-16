<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\LevelDataTable;

class LevelController extends Controller
{
    // public function index() {
    //     // langkah 3
    //     // DB::insert('insert into m_level(level_kode, level_nama, created_at) values(?, ?, ?)', ['CUS', 'Pelanggan', now()]);
    //     // return 'Insert data baru berhasil';

    //     // langkah 5
    //     // $row = DB::update('update m_level set level_nama = ? where level_kode = ?', ['Customer', 'CUS']);
    //     // return 'Update data berhasil. Jumlah data yang diupdate: ' . $row . ' baris';

    //     // langkah 7
    //     // $row = DB::delete('delete from m_level where level_kode = ?', ['CUS']);
    //     // return 'Delete data berhasil. Jumlah data yang dihapus: ' . $row . ' baris';

    //     // langkah 8
    //     // $data = DB::select('select * from m_level');
    //     // return view('level', ['data' => $data]);

    // }

    public function index(LevelDataTable $dataTable) {
        return $dataTable->render('level.index');
    }

    public function create() {
        return view('level.create');
    }

    public function store(Request $request) {
        LevelModel::create([
            'level_kode' => $request->kodeLevel,
            'level_nama' => $request->namaLevel,
        ]);
        return redirect('/level');
    }

    public function edit($id)
    {
        $level = LevelModel::findOrFail($id);
        return view('level.edit', compact('level'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'level_kode' => 'required|string|max:10',
            'level_nama' => 'required|string|max:100',
        ]);

        $level = LevelModel::findOrFail($id);
        $level->update([
            'level_kode' => $request->level_kode,
            'level_nama' => $request->level_nama,
        ]);

        return redirect('/level');
    }

    public function destroy($id)
    {
        $level = LevelModel::findOrFail($id);
        $level->delete();

        return redirect('/level');
    }
}
