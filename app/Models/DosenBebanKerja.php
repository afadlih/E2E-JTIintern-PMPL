<?php
// filepath: app/Models/DosenWorkload.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DosenBebanKerja extends Model
{
    protected $table = 'm_dosen_beban_kerja';

    protected $fillable = [
        'id_dosen',
        'max_mahasiswa',
        'current_mahasiswa'
    ];

    // Relasi ke Dosen
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'id_dosen');
    }

    // Method untuk update jumlah mahasiswa bimbingan
    public function updateCurrentCount()
    {
        $this->current_mahasiswa = Magang::where('id_dosen', $this->id_dosen)
            ->where('status', 'aktif')
            ->count();
        $this->save();

        return $this;
    }
}
