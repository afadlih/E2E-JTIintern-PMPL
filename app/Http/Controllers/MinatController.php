<?php


namespace App\Http\Controllers;

use App\Models\Minat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MinatController extends Controller
{
    public function index()
    {
        $minat = Minat::all();
        return response()->json(['success' => true, 'data' => $minat]);
    }
    
    public function getMinat()
    {
        try {
            $minat = Minat::select('minat_id', 'nama_minat', 'deskripsi')
                ->orderBy('nama_minat')
                ->get();

            Log::info('MinatController getMinat called', [
                'count' => $minat->count(),
                'sample' => $minat->take(1)->toArray()
            ]);

            return response()->json([
                'success' => true,
                'data' => $minat->toArray(),
                'count' => $minat->count(),
                'message' => 'Data minat berhasil diambil'
            ]);
        } catch (\Exception $e) {
            Log::error('MinatController getMinat error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data minat',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // ✅ FIX: show method untuk single minat
    public function show($id)
    {
        try {
            $minat = Minat::where('minat_id', $id)->first();

            if (!$minat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minat tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $minat,
                'message' => 'Detail minat berhasil diambil'
            ]);
        } catch (\Exception $e) {
            Log::error('MinatController show error', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail minat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_minat' => 'required|string|max:255|unique:m_minat,nama_minat',
                'deskripsi' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $minat = Minat::create([
                'nama_minat' => $request->nama_minat,
                'deskripsi' => $request->deskripsi,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Minat berhasil ditambahkan',
                'data' => $minat
            ], 201);
        } catch (\Exception $e) {
            Log::error('MinatController store error', [
                'request' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan minat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $minat = Minat::where('minat_id', $id)->first();

            if (!$minat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minat tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama_minat' => 'required|string|max:255|unique:m_minat,nama_minat,' . $id . ',minat_id',
                'deskripsi' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $minat->update([
                'nama_minat' => $request->nama_minat,
                'deskripsi' => $request->deskripsi,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Minat berhasil diperbarui',
                'data' => $minat
            ]);
        } catch (\Exception $e) {
            Log::error('MinatController update error', [
                'id' => $id,
                'request' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui minat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $minat = Minat::where('minat_id', $id)->first();

            if (!$minat) {
                return response()->json([
                    'success' => false,
                    'message' => 'Minat tidak ditemukan'
                ], 404);
            }

            $minat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Minat berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('MinatController destroy error', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus minat',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
