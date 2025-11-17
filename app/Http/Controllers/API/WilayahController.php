<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;

class WilayahController extends Controller
{
    public function index()
    {
        try {
            $wilayah = Wilayah::all();

            return response()->json([
                'success' => true,
                'data' => $wilayah
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data wilayah: ' . $e->getMessage()
            ], 500);
        }
    }
}
