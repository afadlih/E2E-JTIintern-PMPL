<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPeriode extends Model
{
    protected $table = 't_periode';
    protected $primaryKey = 'id_tperiode';

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id', 'periode_id');
    }
}