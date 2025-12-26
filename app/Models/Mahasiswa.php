<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'm_mahasiswa';
    protected $primaryKey = 'id_mahasiswa';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nim',
        'id_user',
        'id_kelas',
        'ipk',
        'alamat',
        'no_telepon',
        'email',
        'cv',
        'cv_updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function programStudi()
    {
        return $this->belongsTo(Prodi::class, 'kode_prodi', 'kode_prodi');
    }

    public function dosenPembimbing()
    {
        return $this->belongsTo(Dosen::class, 'dosen_pembimbing_id', 'id_dosen');
    }

    public function lamaran()
    {
        return $this->hasMany(Lamaran::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 't_skill_mahasiswa', 'user_id', 'skill_id')
            ->withPivot('lama_skill')
            ->withTimestamps(false); // Add this line
    }

    public function magang()
    {
        return $this->hasOne(Magang::class, 'id_mahasiswa', 'id_mahasiswa');
    }
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function dokumen()
    {
        return $this->hasMany(Dokumen::class, 'id_user', 'id_user');
    }

    public function minat()
    {
        return $this->belongsToMany(Minat::class, 't_minat_mahasiswa', 'mahasiswa_id', 'minat_id');
    }
}
