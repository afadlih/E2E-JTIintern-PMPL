<?php
// filepath: app/Models/SAWCriteria.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SAWKriteria extends Model
{
    protected $table = 'm_saw_kriteria';
    
    protected $fillable = [
        'name',
        'code',
        'weight',
        'description'
    ];
    
    // Method untuk mendapatkan semua bobot kriteria
    public static function getAllWeights()
    {
        return self::pluck('weight', 'code')->toArray();
    }
}