<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkillLowongan extends Model
{
    use HasFactory;

    protected $table = 't_skill_lowongan';
    public $timestamps = true;

    protected $fillable = [
        'id_lowongan',
        'id_skill'
    ];

    // Relationship with Lowongan
    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'id_lowongan', 'id_lowongan');
    }

    // Relationship with Skill
    public function skill()
    {
        return $this->belongsTo(Skill::class, 'id_skill', 'skill_id');
    }
}