<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;
    protected $table = 'm_perusahaan';
    protected $primaryKey = 'perusahaan_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_perusahaan',
        'alamat_perusahaan',
        'wilayah_id',
        'contact_person',
        'email',
        'instagram',
        'website',
        'deskripsi',
        'gmaps',
        'logo',
    ];

    // ✅ TAMBAHKAN: Accessor untuk logo URL
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            // Jika path sudah lengkap dengan storage/, return as is
            if (strpos($this->logo, 'storage/') === 0) {
                return asset($this->logo);
            }
            // Jika hanya path relatif, tambahkan storage/
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    // ✅ TAMBAHKAN: Accessor untuk logo path
    public function getLogoPathAttribute()
    {
        if ($this->logo) {
            // Hapus 'storage/' dari awal jika ada untuk mendapatkan path asli
            return str_replace('storage/', '', $this->logo);
        }
        return null;
    }

    // Relasi ke tabel m_wilayah
    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id', 'wilayah_id');
    }

    // Relasi ke tabel t_lowongan
    public function lowongan()
    {
        return $this->hasMany(Lowongan::class, 'perusahaan_id', 'perusahaan_id');
    }
}
