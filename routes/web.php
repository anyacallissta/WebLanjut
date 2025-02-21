<?php

use Illuminate\Support\Facades\Route; // Menggunakan facade Route untuk mendefinisikan rute
use App\Http\Controllers\ItemController; // Mengimpor ItemController agar bisa digunakan tanpa menuliskan namespacenya
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

// jobsheet 2
Route::get('/hello', function () {
    return 'Hello World';
});

Route::get('/world', function () {
    return 'World';
});

Route::get('/', function () {
    return 'Selamat Datang';
});

Route::get('/about', function () {
    return 'NIM: 2341720234 | Nama: Anya Callissta Chriswantari';
});

Route::get('/user/{name}', function ($name) {
    return 'Nama Saya '.$name;
});

Route::get('/posts/{post}/comments/{comment}', function ($postId, $commentId) {
    return 'Pos ke-'.$postId." Komentar ke-: ".$commentId;
});

Route::get('/articles/{id}', function ($id) {
    return 'Halaman Artikel dengan ID ' .$id;
});

// Route::get('/user/{name?}', function ($name=null) {
//     return 'Nama Saya '.$name;
// });

Route::get('/user/{name?}', function ($name='John') {
    return 'Nama saya '.$name;
});

