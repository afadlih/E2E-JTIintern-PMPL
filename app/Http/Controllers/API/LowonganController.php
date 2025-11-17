<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lowongan;
use App\Services\KapasitasLowonganService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Skill;
use App\Models\Jenis;

class LowonganController extends Controller
{
    protected $kapasitasService;

    public function __construct(KapasitasLowonganService $kapasitasService)
    {
        $this->kapasitasService = $kapasitasService;
    }

    public function index(Request $request)
    {
        try {
            // Get lowongan without eager loading skills
            $query = Lowongan::with(['perusahaan.wilayah'])
                     ->orderBy('created_at', 'desc');

            if ($request->has('perusahaan_id') && $request->perusahaan_id) {
                $query->where('perusahaan_id', $request->perusahaan_id);
            }

            $lowongan = $query->get();

            // Get all lowongan IDs
            $lowonganIds = $lowongan->pluck('id_lowongan')->toArray();

            // Get skills manually
            $skillsData = DB::table('t_skill_lowongan as tsl')
                ->join('m_skill as ms', 'tsl.id_skill', '=', 'ms.skill_id')
                ->whereIn('tsl.id_lowongan', $lowonganIds)
                ->select('tsl.id_lowongan', 'ms.skill_id', 'ms.nama')
                ->get();

            // Group skills by lowongan ID
            $skillsByLowongan = [];
            foreach ($skillsData as $skill) {
                $skillsByLowongan[$skill->id_lowongan][] = [
                    'skill_id' => $skill->skill_id,
                    'nama' => $skill->nama
                ];
            }

            $formattedLowongan = $lowongan->map(function ($item) use ($skillsByLowongan) {
                return [
                    'id_lowongan' => $item->id_lowongan,
                    'judul_lowongan' => $item->judul_lowongan,
                    'deskripsi' => $item->deskripsi,
                    'kapasitas' => $item->kapasitas,
                    'perusahaan' => [
                        'nama_perusahaan' => $item->perusahaan->nama_perusahaan ?? 'Tidak Diketahui',
                        'nama_kota' => $item->perusahaan->wilayah->nama_kota ?? 'Tidak Diketahui',
                    ],
                    'skills' => $skillsByLowongan[$item->id_lowongan] ?? [],
                    'created_at' => $item->created_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedLowongan
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching lowongan: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data lowongan: ' . $e->getMessage()
            ], 500);
        }
    }
    public function store(Request $request)
    {
        $request->validate([
            'judul_lowongan' => 'required|string|max:255',
            'perusahaan_id' => 'required|exists:m_perusahaan,perusahaan_id',
            'periode_id' => 'required|exists:m_periode,periode_id',
            'skill_id' => 'required|array',
            'skill_id.*' => 'exists:m_skill,skill_id',
            'jenis_id' => 'required|exists:m_jenis,jenis_id',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'required|string',
        ]);

        try {
            DB::beginTransaction();
            
            // Create lowongan
            $lowongan = new Lowongan();
            $lowongan->judul_lowongan = $request->judul_lowongan;
            $lowongan->perusahaan_id = $request->perusahaan_id;
            $lowongan->periode_id = $request->periode_id;
            $lowongan->jenis_id = $request->jenis_id;
            $lowongan->kapasitas = $request->kapasitas;
            $lowongan->deskripsi = $request->deskripsi;
            $lowongan->save();
            
            // Add skills
            foreach ($request->skill_id as $skillId) {
                DB::table('t_skill_lowongan')->insert([
                    'id_lowongan' => $lowongan->id_lowongan,
                    'id_skill' => $skillId
                ]);
            }
            
            // Initialize capacity record
            $this->kapasitasService->initializeKapasitas($lowongan->id_lowongan, $request->kapasitas);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Lowongan berhasil ditambahkan.',
                'data' => $lowongan
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding lowongan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan lowongan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            // Load lowongan without the skills relationship
            $lowongan = Lowongan::with(['perusahaan', 'periode', 'jenis'])->findOrFail($id);
            
            // Get capacity information
            $kapasitas = DB::table('t_kapasitas_lowongan')
                ->where('id_lowongan', $id)
                ->first();
            
            // Get skills manually for this specific lowongan
            $skills = DB::table('t_skill_lowongan as tsl')
                ->join('m_skill as ms', 'tsl.id_skill', '=', 'ms.skill_id')
                ->where('tsl.id_lowongan', $id)
                ->select('ms.skill_id', 'ms.nama')
                ->get();
            
            // Add debug info
            Log::info('Showing lowongan with ID: ' . $id);
            Log::info('Skills found: ' . $skills->count());
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id_lowongan' => $lowongan->id_lowongan,
                    'judul_lowongan' => $lowongan->judul_lowongan,
                    'kapasitas' => $lowongan->kapasitas,
                    'kapasitas_tersedia' => $kapasitas ? $kapasitas->kapasitas_tersedia : null,
                    'kapasitas_total' => $kapasitas ? $kapasitas->kapasitas_total : $lowongan->kapasitas,
                    'deskripsi' => $lowongan->deskripsi,
                    'perusahaan' => [
                        'perusahaan_id' => $lowongan->perusahaan->perusahaan_id,
                        'nama_perusahaan' => $lowongan->perusahaan->nama_perusahaan ?? 'Tidak Diketahui',
                    ],
                    'periode' => [
                        'periode_id' => $lowongan->periode->periode_id,
                        'waktu' => $lowongan->periode->waktu ?? 'Tidak Diketahui',
                    ],
                    'skills' => $skills->map(function($skill) {
                        return [
                            'skill_id' => $skill->skill_id,
                            'nama' => $skill->nama ?? 'Tidak Diketahui',
                        ];
                    }),
                    'jenis' => [
                        'jenis_id' => $lowongan->jenis->jenis_id,
                        'nama_jenis' => $lowongan->jenis->nama_jenis ?? 'Tidak Diketahui',
                    ],
                    'created_at' => $lowongan->created_at,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching lowongan detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail lowongan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_lowongan' => 'required|string|max:255',
            'perusahaan_id' => 'required|exists:m_perusahaan,perusahaan_id',
            'periode_id' => 'required|exists:m_periode,periode_id',
            'jenis_id' => 'required|exists:m_jenis,jenis_id',
            'kapasitas' => 'required|integer|min:1',
            'deskripsi' => 'required|string',
            'skill_id' => 'required|array',
            'skill_id.*' => 'exists:m_skill,skill_id'
        ]);

        try {
            // Use DB transaction to ensure data integrity
            DB::beginTransaction();

            // Find the lowongan record
            $lowongan = Lowongan::findOrFail($id);
            $oldKapasitas = $lowongan->kapasitas;
            
            // Update the main fields
            $lowongan->judul_lowongan = $request->judul_lowongan;
            $lowongan->perusahaan_id = $request->perusahaan_id;
            $lowongan->periode_id = $request->periode_id;
            $lowongan->jenis_id = $request->jenis_id;
            $lowongan->kapasitas = $request->kapasitas;
            $lowongan->deskripsi = $request->deskripsi;
            $lowongan->save();
            
            // Log skill IDs for debugging
            Log::info('Updating skills for lowongan ' . $id . ' with skill_ids: ', $request->skill_id);
            
            // Delete existing skills first
            DB::table('t_skill_lowongan')->where('id_lowongan', $id)->delete();
            
            // Add the new skills
            foreach ($request->skill_id as $skillId) {
                DB::table('t_skill_lowongan')->insert([
                    'id_lowongan' => $id,
                    'id_skill' => $skillId
                    // Remove created_at and updated_at
                ]);
            }
            
            // Update kapasitas if changed
            if ($oldKapasitas != $request->kapasitas) {
                $this->kapasitasService->updateKapasitasTotal($id, $request->kapasitas);
            }
            
            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lowongan berhasil diperbarui',
                'data' => $lowongan
            ]);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error('Error updating lowongan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui lowongan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // First delete the associated skills
            DB::table('t_skill_lowongan')->where('id_lowongan', $id)->delete();
            
            // Then delete the lowongan itself
            $lowongan = Lowongan::findOrFail($id);
            $lowongan->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lowongan berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting lowongan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus lowongan.',
            ], 500);
        }
    }

    public function getSkill()
    {
        try {
            $skills = Skill::all();
            return response()->json([
                'success' => true,
                'data' => $skills
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data skill.'
            ], 500);
        }
    }

    public function getJenis()
    {
        try {
            $jenis = Jenis::all();
            return response()->json([
                'success' => true,
                'data' => $jenis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data jenis.'
            ], 500);
        }
    }

    // Add this method to your controller
    public function getAvailableCapacity($id)
    {
        try {
            $lowongan = Lowongan::findOrFail($id);
            $kapasitas = $lowongan->kapasitas()->first();
            
            if (!$kapasitas) {
                // Sync capacity record if it doesn't exist
                $this->kapasitasService->syncKapasitas($id);
                $kapasitas = $lowongan->kapasitas()->first();
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id_lowongan' => $id,
                    'kapasitas_total' => $kapasitas ? $kapasitas->kapasitas_total : $lowongan->kapasitas,
                    'kapasitas_tersedia' => $kapasitas ? $kapasitas->kapasitas_tersedia : 0
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting capacity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat informasi kapasitas: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add this method to your LowonganController
    public function syncCapacity($id)
    {
        try {
            $result = $this->kapasitasService->syncKapasitas($id);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data kapasitas berhasil disinkronkan'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyinkronkan data kapasitas'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error syncing capacity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}