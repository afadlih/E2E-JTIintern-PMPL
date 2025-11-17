<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class dataMhsController extends Controller
{
    public function index()
    {
        return view('pages.data_mahasiswa'); 
    }
public function getData()
{
    $mahasiswa = Mahasiswa::with(['user', 'skills', 'magang', 'kelas.prodi'])
        ->get()
        ->map(function ($mahasiswa) {
            return [
                'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                'user' => [
                    'name' => $mahasiswa->user ? $mahasiswa->user->name : '-',
                    'email' => $mahasiswa->user ? $mahasiswa->user->email : '-'
                ],
                'nim' => $mahasiswa->nim,
                'kelas' => [
                    'nama' => $mahasiswa->kelas ? $mahasiswa->kelas->nama_kelas : '-',
                    'prodi' => $mahasiswa->kelas ? $mahasiswa->kelas->prodi->nama_prodi : '-',
                    'tahun_masuk' => $mahasiswa->kelas ? $mahasiswa->kelas->tahun_masuk : '-'
                ],
                'status_magang' => $mahasiswa->magang ? 'Sedang Magang' : 'Belum Magang',
                'skills' => $mahasiswa->skills->map(function ($skill) {
                    return [
                        'nama_skill' => $skill->nama
                    ];
                })
            ];
        });

    return response()->json([
        'success' => true,
        'data' => $mahasiswa
    ]);
}
public function getKelas()
{
    $kelas = Kelas::with('prodi')->get()->map(function($kelas) {
        return [
            'nama_kelas' => $kelas->nama_kelas,
            'prodi' => [
                'kode' => $kelas->kode_prodi,
                'nama_prodi' => $kelas->prodi->nama_prodi
            ],
            'tahun_masuk' => $kelas->tahun_masuk
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $kelas
    ]);
}

public function tambahMahasiswa(Request $request)
{
    $mahasiswa = new Mahasiswa();
    $mahasiswa->nama = $request->input('nama');
    $mahasiswa->nim = $request->input('nim');
    $mahasiswa->id_kelas = $request->input('nama_kelas'); 
    $mahasiswa->alamat = $request->input('alamat');
    $mahasiswa->ipk = $request->input('ipk');
    $mahasiswa->save();

    return response()->json([
        'success' => true,
        'message' => 'Mahasiswa berhasil ditambahkan'
    ]);

}
}
