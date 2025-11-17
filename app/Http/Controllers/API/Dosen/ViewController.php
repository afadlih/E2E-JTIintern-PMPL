<?php

namespace App\Http\Controllers\API\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    public function dashboard()
    {
        return view('pages.dosen.dashboard', [
            'title' => 'Dashboard Dosen'
        ]);
    }

    public function profile()
    {
        return view('pages.dosen.dosenProfile', [
            'title' => 'Profil Dosen'
        ]);
    }

    public function mahasiswa()
    {
        return view('pages.dosen.dosenMahasiswa', [
            'title' => 'Data Mahasiswa'
        ]);
    }

    public function evaluasi()
    {
        return view('pages.dosen.evaluasi', [
            'title' => 'Evaluasi Dosen'
        ]);
    }
}