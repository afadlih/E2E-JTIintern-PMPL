<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminMahasiswaController extends Controller
{
    /**
     * Display a listing of mahasiswa.
     */
    public function index(Request $request)
    {
        $query = Mahasiswa::with(['user', 'kelas']);

        // Filter by kelas_id if provided
        if ($request->has('kelas_id')) {
            $query->where('id_kelas', $request->get('kelas_id'));
        }

        $mahasiswa = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => $mahasiswa->map(function($mhs) {
                return [
                    'id_mahasiswa' => $mhs->id_mahasiswa,
                    'nim' => $mhs->nim,
                    'nama' => $mhs->user->name ?? '',
                    'email' => $mhs->user->email ?? '',
                    'ipk' => $mhs->ipk,
                    'kelas' => $mhs->kelas->nama_kelas ?? '',
                ];
            })
        ]);
    }

    /**
     * Display the specified mahasiswa.
     */
    public function show($id)
    {
        $mahasiswa = Mahasiswa::with(['user', 'kelas'])->find($id);

        if (!$mahasiswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mahasiswa not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [[
                'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->user->name ?? '',
                'email' => $mahasiswa->user->email ?? '',
                'ipk' => $mahasiswa->ipk,
                'kelas' => $mahasiswa->kelas->nama_kelas ?? '',
            ]]
        ]);
    }

    /**
     * Store a newly created mahasiswa.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'required|unique:m_mahasiswa,nim',
            'nama' => 'required|string',
            'email' => 'required|email|unique:m_user,email',
            'password' => 'required|min:6',
            'id_kelas' => 'required|exists:m_kelas,id_kelas',
            'alamat' => 'nullable|string|max:50',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'telp' => 'nullable|string|max:25',
        ]);

        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'mahasiswa',
            ]);

            // Create mahasiswa
            $mahasiswa = Mahasiswa::create([
                'id_user' => $user->id_user,
                'nim' => $validated['nim'],
                'id_kelas' => $validated['id_kelas'],
                'alamat' => $validated['alamat'] ?? null,
                'ipk' => $validated['ipk'] ?? null,
                'telp' => $validated['telp'] ?? null,
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Mahasiswa berhasil ditambahkan',
                'data' => $mahasiswa
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create mahasiswa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified mahasiswa.
     */
    public function update(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::find($id);

        if (!$mahasiswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mahasiswa not found'
            ], 404);
        }

        $validated = $request->validate([
            'nim' => 'sometimes|unique:m_mahasiswa,nim,' . $id . ',id_mahasiswa',
            'nama' => 'sometimes|string',
            'email' => ['sometimes', 'email', Rule::unique('m_user', 'email')->ignore($mahasiswa->id_user, 'id_user')],
            'id_kelas' => 'sometimes|exists:m_kelas,id_kelas',
            'alamat' => 'nullable|string|max:50',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'telp' => 'nullable|string|max:25',
        ]);

        DB::beginTransaction();
        try {
            // Update mahasiswa
            $mahasiswa->update([
                'nim' => $validated['nim'] ?? $mahasiswa->nim,
                'id_kelas' => $validated['id_kelas'] ?? $mahasiswa->id_kelas,
                'alamat' => $validated['alamat'] ?? $mahasiswa->alamat,
                'ipk' => $validated['ipk'] ?? $mahasiswa->ipk,
                'telp' => $validated['telp'] ?? $mahasiswa->telp,
            ]);

            // Update user if name provided
            if ($mahasiswa->user && (isset($validated['nama']) || isset($validated['email']))) {
                $mahasiswa->user->update([
                    'name' => $validated['nama'] ?? $mahasiswa->user->name,
                    'email' => $validated['email'] ?? $mahasiswa->user->email,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Mahasiswa berhasil diupdate',
                'data' => $mahasiswa
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update mahasiswa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified mahasiswa.
     */
    public function destroy($id)
    {
        $mahasiswa = Mahasiswa::find($id);

        if (!$mahasiswa) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mahasiswa not found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $userId = $mahasiswa->id_user;
            $mahasiswa->delete();

            // Delete associated user
            if ($userId) {
                User::find($userId)?->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Mahasiswa berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete mahasiswa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search mahasiswa by name or NIM.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $mahasiswa = Mahasiswa::with(['user', 'kelas'])
            ->where('nim', 'like', "%{$query}%")
            ->orWhereHas('user', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $mahasiswa
        ]);
    }

    /**
     * Filter mahasiswa by kelas.
     */
    public function filterByKelas(Request $request)
    {
        $kelasId = $request->get('kelas_id');

        $mahasiswa = Mahasiswa::with(['user', 'kelas'])
            ->where('id_kelas', $kelasId)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $mahasiswa
        ]);
    }
}
