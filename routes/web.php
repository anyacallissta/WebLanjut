<?php

use Illuminate\Support\Facades\Route; // Menggunakan facade Route untuk mendefinisikan rute

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () { // Rute untuk halaman utama ('/'), mengembalikan tampilan 'welcome'
    return view('welcome'); // Menampilkan view 'welcome.blade.php'
});

Route::resource('items', ItemController::class); // Menyediakan resource route dan secara otomatis membuat rute CRUD