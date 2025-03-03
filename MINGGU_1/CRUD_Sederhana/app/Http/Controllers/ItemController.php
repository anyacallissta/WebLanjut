<?php

namespace App\Http\Controllers; // Menentukan namespace untuk controller

use App\Models\Item; // Menggunakan model Item
use Illuminate\Http\Request; // Menggunakan Request untuk menangani input pengguna

class ItemController extends Controller // Mendefinisikan ItemController yang mewarisi Controller
{
    public function index() // Menampilkan daftar semua item
    {
        $items = Item::all(); // Mengambil semua data item dari database
        return view('items.index', compact('items')); // Menampilkan view dengan data item
    }

    public function create() // Menampilkan form untuk membuat item baru
    {
        return view('items.create');
    }

    public function store(Request $request) // Menyimpan item baru ke dalam database
    {
        $request->validate([ // Validasi input
            'name' => 'required', // Nama wajib diisi
            'description' => 'required', // Deskripsi wajib diisi
        ]);
         
        //Item::create($request->all()); 
        //return redirect()->route('items.index'); 

        // Hanya masukkan atribut yang diizinkan
         Item::create($request->only(['name', 'description'])); // Menyimpan data item dengan atribut yang diizinkan
        return redirect()->route('items.index')->with('success', 'Item added successfully.'); // Redirect ke halaman daftar item dengan pesan sukses
    }

    public function show(Item $item) // Menampilkan detail satu item berdasarkan ID
    {
        return view('items.show', compact('item'));
    }

    public function edit(Item $item) // Menampilkan form edit item
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item) // Memperbarui item dalam database
    {
        $request->validate([ // Validasi input
            'name' => 'required', // Nama wajib diisi
            'description' => 'required', // Deskripsi wajib diisi
        ]);
         
        //$item->update($request->all());
        //return redirect()->route('items.index');

        // Hanya masukkan atribut yang diizinkan
         $item->update($request->only(['name', 'description'])); // Memperbarui data item dengan atribut yang diizinkan
        return redirect()->route('items.index')->with('success', 'Item updated successfully.'); // Redirect ke halaman daftar item dengan pesan sukses
    }

    public function destroy(Item $item) // Menghapus item dari database
    {
        
       // return redirect()->route('items.index');
       $item->delete(); // Menghapus item dari database
       return redirect()->route('items.index')->with('success', 'Item deleted successfully.'); // Redirect ke halaman daftar item dengan pesan sukses
    }
}