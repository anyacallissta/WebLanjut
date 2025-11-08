<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Pastikan ini di-import

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data supplier yang akan dimasukkan
        $suppliers = [
            [
                'supplier_kode' => 'S-0001',
                'supplier_nama' => 'PT Indah Sari Makmur',
                'supplier_alamat' => 'Jalan Merdeka No. 45, Jakarta Pusat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supplier_kode' => 'S-0002',
                'supplier_nama' => 'Distributor Jaya Abadi',
                'supplier_alamat' => 'Ruko Sentosa Blok A1, Surabaya',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supplier_kode' => 'S-0003',
                'supplier_nama' => 'Toko Bahan Baku Sentral',
                'supplier_alamat' => 'Komplek Pergudangan Cipta, Bandung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Masukkan data ke tabel m_supplier
        DB::table('m_supplier')->insert($suppliers);
    }
}