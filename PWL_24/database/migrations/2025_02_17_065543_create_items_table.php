<?php

use Illuminate\Database\Migrations\Migration; // Menggunakan class Migration untuk membuat database migration
use Illuminate\Database\Schema\Blueprint; // Menggunakan Blueprint untuk mendefinisikan struktur tabel
use Illuminate\Support\Facades\Schema; // Menggunakan Schema untuk menjalankan operasi terhadap database

return new class extends Migration // Mengembalikan instance class anonim yang mewakili migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) { // Membuat tabel 'items' jika belum ada
            $table->id(); // Membuat kolom 'id' sebagai primary key dengan auto-increment
            $table->string('name'); // Membuat kolom 'name' bertipe string (VARCHAR)
            $table->text('description'); // Membuat kolom 'description' bertipe TEXT
            $table->timestamps(); // Menambahkan kolom 'created_at' dan 'updated_at' secara otomatis
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items'); // Menghapus tabel 'items' jika ada
    }
};
