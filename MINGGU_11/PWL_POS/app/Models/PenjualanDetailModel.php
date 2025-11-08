<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetailModel extends Model
{
    use HasFactory;

    protected $table = 't_penjualan_detail';
    protected $primaryKey = 'detail_id';

    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'harga',
        'jumlah',
    ];

    // Relasi ke tabel penjualan (t_penjualan)
    public function penjualan()
    {
        return $this->belongsTo(PenjualanModel::class, 'penjualan_id', 'penjualan_id');
    }

    // Relasi ke tabel barang (m_barang)
    public function barang()
    {
        return $this->belongsTo(BarangModel::class, 'barang_id', 'barang_id');
    }

    /**
     * Event otomatis untuk update stok barang saat detail penjualan dibuat atau dihapus.
     */
    protected static function booted()
    {
        // Saat detail penjualan dibuat → kurangi stok
        static::created(function ($detail) {
            StokModel::create([
                'barang_id'    => $detail->barang_id,
                'user_id'      => auth()->id() ?? 1,
                'stok_tanggal' => now(),
                'stok_jumlah'  => -$detail->jumlah,
            ]);
        });

        // Saat detail penjualan dihapus → kembalikan stok
        static::deleted(function ($detail) {
            StokModel::create([
                'barang_id'    => $detail->barang_id,
                'user_id'      => auth()->id() ?? 1,
                'stok_tanggal' => now(),
                'stok_jumlah'  => $detail->jumlah,
            ]);
        });
    }
}
