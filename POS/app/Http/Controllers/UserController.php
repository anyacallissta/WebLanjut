<?php

namespace App\Http\Controllers;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // public function profile($id, $name) {
    //     return view('user', ['id' => $id, 'name' => $name]);
    // }

    // jobsheet 3
    public function index() {
    // tambah data user dengan Eloquent Model
        $data = [
            'nama' => 'Pelanggan Pertama',
        ];

        UserModel::where('username', 'customer-1') -> update($data);

        // coba akses model UserModel
        $user = UserModel::all(); // ambil semua data dari tabel m_user
        return view('user', ['data' => $user]);
    }
}