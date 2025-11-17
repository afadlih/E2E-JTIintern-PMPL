<?php
// filepath: app/Models/SkillDosen.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillDosen extends Model
{
    protected $table = 't_skill_dosen';
    
    protected $fillable = [
        'id_dosen',
        'id_skill'
    ];
    
    // Relasi ke Dosen
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'id_dosen');
    }
    
    // Relasi ke Skill
    public function skill()
    {
        return $this->belongsTo(Skill::class, 'id_skill', 'id_skill');
    }
}