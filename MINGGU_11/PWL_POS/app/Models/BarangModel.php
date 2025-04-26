<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BarangModel extends Model
{
    use HasFactory;

    protected $table = 'm_barang';
    protected $primaryKey = 'barang_id';
    // protected $fillable = [
    //     'barang_id',
    //     'kategori_id',
    //     'barang_kode',
    //     'barang_nama',
    //     'harga_beli',
    //     'harga_jual',
    // ];

    // tugas jobsheet 11
    protected $fillable = [
        'kategori_id',
        'barang_kode',
        'barang_nama',
        'harga_beli',
        'harga_jual',
        'image',
        'created_at',
        'updated_at'
    ];

    public function kategori():BelongsTo
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
    }

    // tugas jobsheet 11
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn($image) => url('/storage/barang/' . $image),
        );
    }
}
  