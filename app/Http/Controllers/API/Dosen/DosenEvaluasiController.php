<?php

namespace App\Http\Controllers\API\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DosenEvaluasiController extends Controller
{
    public function index()
    {
        return view('pages.dosen.evaluasi', [
            'title' => 'Evaluasi Dosen',
        ]);
    }
}
