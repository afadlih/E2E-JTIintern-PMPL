<?php
// filepath: app/Models/PlottingHistory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlottingRiwayat extends Model
{
    protected $table = 't_plotting_riwayat';
    
    protected $fillable = [
        'id_magang',
        'id_dosen',
        'score',
        'wilayah_score',
        'skill_score',
        'assigned_at'
    ];
    
    // Relasi ke Magang
    public function magang()
    {
        return $this->belongsTo(Magang::class, 'id_magang', 'id_magang');
    }
    
    // Relasi ke Dosen
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'id_dosen');
    }
}