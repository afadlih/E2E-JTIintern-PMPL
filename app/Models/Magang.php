<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Magang extends Model
{
    use HasFactory;

    protected $table = 'm_magang';
    protected $primaryKey = 'id_magang';

    protected $fillable = [
        'id_lowongan',
        'id_mahasiswa', 
        'id_dosen',
        'status',
        'tgl_mulai',
        'tgl_selesai'
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date'
    ];

    // ✅ Method untuk menghitung progress
    public function getProgressAttribute()
    {
        if (!$this->tgl_mulai || !$this->tgl_selesai) {
            return 0;
        }

        $startDate = Carbon::parse($this->tgl_mulai);
        $endDate = Carbon::parse($this->tgl_selesai);
        $today = Carbon::today();

        // Jika belum mulai
        if ($today->isBefore($startDate)) {
            return 0;
        }

        // Jika sudah selesai
        if ($today->isAfter($endDate)) {
            return 100;
        }

        // Hitung progress
        $totalDays = $startDate->diffInDays($endDate);
        $passedDays = $startDate->diffInDays($today);

        return $totalDays > 0 ? round(($passedDays / $totalDays) * 100, 2) : 0;
    }

    // ✅ Method untuk menghitung hari yang lewat
    public function getHariLewatAttribute()
    {
        if (!$this->tgl_mulai) {
            return 0;
        }

        $startDate = Carbon::parse($this->tgl_mulai);
        $today = Carbon::today();

        // Jika belum mulai
        if ($today->isBefore($startDate)) {
            return 0;
        }

        return $startDate->diffInDays($today);
    }

    // ✅ Method untuk menghitung sisa hari
    public function getSisaHariAttribute()
    {
        if (!$this->tgl_selesai) {
            return 0;
        }

        $endDate = Carbon::parse($this->tgl_selesai);
        $today = Carbon::today();

        // Jika sudah selesai
        if ($today->isAfter($endDate)) {
            return 0;
        }

        return $today->diffInDays($endDate);
    }

    // ✅ Method untuk menghitung total durasi
    public function getTotalDurasiAttribute()
    {
        if (!$this->tgl_mulai || !$this->tgl_selesai) {
            return 0;
        }

        return Carbon::parse($this->tgl_mulai)->diffInDays(Carbon::parse($this->tgl_selesai));
    }

    // ✅ Method untuk status magang berdasarkan tanggal
    public function getStatusMagangAttribute()
    {
        if (!$this->tgl_mulai || !$this->tgl_selesai) {
            return 'belum_terjadwal';
        }

        $today = Carbon::today();
        $startDate = Carbon::parse($this->tgl_mulai);
        $endDate = Carbon::parse($this->tgl_selesai);

        if ($today->isBefore($startDate)) {
            return 'belum_mulai';
        } elseif ($today->isAfter($endDate)) {
            return 'selesai';
        } else {
            return 'berlangsung';
        }
    }

    // Relationships
    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'id_lowongan', 'id_lowongan');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'id_dosen');
    }
}
