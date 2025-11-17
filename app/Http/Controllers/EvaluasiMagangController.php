<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class EvaluasiMagangController extends Controller
{
    /**
     * ✅ CHECK: Apakah magang expired dan perlu evaluasi
     */
    public function checkNeedEvaluation($idMagang)
    {
        try {
            Log::info('🔍 Checking evaluation need for magang:', ['id_magang' => $idMagang]);

            // ✅ CHECK: Magang dengan status aktif atau selesai
            $magang = DB::table('m_magang as m')
                ->leftJoin('m_lowongan as low', 'm.id_lowongan', '=', 'low.id_lowongan')
                ->leftJoin('m_perusahaan as p', 'low.perusahaan_id', '=', 'p.perusahaan_id')
                ->where('m.id_magang', $idMagang)
                ->whereIn('m.status', ['aktif', 'selesai'])
                ->select([
                    'm.id_magang',
                    'm.tgl_selesai',
                    'm.status',
                    'p.nama_perusahaan',
                    'low.judul_lowongan'
                ])
                ->first();

            if (!$magang) {
                Log::warning('❌ Magang not found or invalid status:', ['id_magang' => $idMagang]);
                return response()->json([
                    'success' => false,
                    'message' => 'Magang tidak ditemukan'
                ], 404);
            }

            // ✅ CHECK: Apakah sudah ada evaluasi (sesuai database schema)
            $hasEvaluation = DB::table('t_evaluasi')
                ->where('id_magang', $idMagang)
                ->whereNotNull('nilai_perusahaan') // ✅ CHECK: nilai sudah diinput
                ->exists();

            if ($hasEvaluation) {
                Log::info('✅ Evaluation already exists:', ['id_magang' => $idMagang]);
                return response()->json([
                    'success' => false,
                    'message' => 'Evaluasi sudah pernah disubmit',
                    'need_evaluation' => false
                ]);
            }

            // ✅ CHECK: Apakah sudah expired atau status selesai
            $today = Carbon::now();
            $endDate = Carbon::parse($magang->tgl_selesai);
            $isExpired = $today->gt($endDate);
            $isCompleted = $magang->status === 'selesai';
            $needEvaluation = $isExpired || $isCompleted;
            
            $daysExpired = $isExpired ? $endDate->diffInDays($today) : 0;

            Log::info('📊 Evaluation check result:', [
                'id_magang' => $idMagang,
                'status' => $magang->status,
                'tgl_selesai' => $magang->tgl_selesai,
                'is_expired' => $isExpired,
                'is_completed' => $isCompleted,
                'need_evaluation' => $needEvaluation,
                'days_expired' => $daysExpired
            ]);

            return response()->json([
                'success' => true,
                'need_evaluation' => $needEvaluation,
                'magang' => $magang,
                'days_expired' => $daysExpired,
                'status' => $isCompleted ? 'completed' : ($isExpired ? 'expired' : 'active'),
                'message' => $needEvaluation 
                    ? ($isCompleted 
                        ? "Magang sudah selesai, silakan input nilai dari pengawas lapangan"
                        : "Magang sudah selesai {$daysExpired} hari yang lalu, silakan input nilai dari pengawas lapangan")
                    : "Magang masih aktif, evaluasi akan tersedia setelah tanggal selesai"
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking evaluation need: ' . $e->getMessage(), [
                'id_magang' => $idMagang,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }

    /**
     * ✅ SUBMIT: Nilai evaluasi dari mahasiswa (same as before)
     */
public function submitEvaluasi(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'id_magang' => 'required|exists:m_magang,id_magang',
            'nilai_perusahaan' => 'required|numeric|min:0|max:100',
            'file_nilai_perusahaan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all())
            ], 422);
        }

        $idMagang = $request->id_magang;

        // Process and store the file
        $filePath = null;
        if ($request->hasFile('file_nilai_perusahaan')) {
            $file = $request->file('file_nilai_perusahaan');
            $fileName = 'nilai_' . $idMagang . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('evaluasi/nilai_perusahaan', $fileName, 'public');
        }

        // Begin transaction
        DB::beginTransaction();

        try {
            // Insert data into t_evaluasi table
            $evaluasiId = DB::table('t_evaluasi')->insertGetId([
                'id_magang' => $idMagang,
                'nilai_perusahaan' => $request->nilai_perusahaan,
                'file_penilaian_perusahaan' => $filePath,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Commit transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Evaluasi magang berhasil disimpan',
                'data' => ['id_evaluasi' => $evaluasiId]
            ]);
        } catch (\Exception $e) {
            // Rollback transaction if error
            DB::rollback();
            throw $e;
        }

    } catch (\Exception $e) {
        Log::error('Error submitting evaluasi: ' . $e->getMessage(), [
            'request' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat menyimpan evaluasi'
        ], 500);
    }
}

    /**
     * ✅ GET: Status evaluasi mahasiswa
     */
    public function getEvaluasiStatus($idMagang)
    {
        try {
            Log::info('🔍 Checking evaluasi status for magang:', ['id_magang' => $idMagang]);
            
            // ✅ FIX: Sesuaikan dengan schema database dan simplify query
            $evaluasi = DB::table('t_evaluasi')
                ->where('id_magang', $idMagang)
                ->first();
            
            if (!$evaluasi) {
                Log::info('❓ No evaluation found for magang:', ['id_magang' => $idMagang]);
                return response()->json([
                    'success' => false,
                    'message' => 'Evaluasi tidak ditemukan',
                    'data' => null
                ]);
            }

            Log::info('✅ Evaluation found for magang:', ['id_magang' => $idMagang, 'eval_id' => $evaluasi->id_evaluasi]);
            
            return response()->json([
                'success' => true,
                'message' => 'Evaluasi ditemukan',
                'data' => $evaluasi
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting evaluasi status: ' . $e->getMessage(), [
                'id_magang' => $idMagang,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * ✅ SEND: Notification ke dosen pembimbing (same as before)
     */
    private function sendEvaluationNotificationToDosen($magang, $evaluasiId)
    {
        try {
            if (!isset($magang->id_dosen)) {
                return;
            }

            // Get dosen user ID
            $dosen = DB::table('m_dosen')->where('id_dosen', $magang->id_dosen)->first();
            if (!$dosen || !$dosen->id_user) {
                return;
            }

            $mahasiswa = DB::table('m_mahasiswa as mhs')
                ->leftJoin('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->where('mhs.id_mahasiswa', $magang->id_mahasiswa)
                ->first();

            $notificationData = [
                'id_user' => $dosen->id_user,
                'judul' => 'Evaluasi Magang Baru 📝',
                'pesan' => "Mahasiswa {$mahasiswa->name} telah mengsubmit evaluasi magang. Silakan review dan berikan penilaian.",
                'kategori' => 'evaluasi_dosen',
                'jenis' => 'info',
                'is_important' => true,
                'is_read' => false,
                'data_terkait' => json_encode([
                    'id_evaluasi' => $evaluasiId,
                    'id_magang' => $magang->id_magang,
                    'action_required' => 'review_evaluasi'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ];

            $notificationTables = ['m_notifikasi', 't_notifikasi', 'notifications'];
            foreach ($notificationTables as $tableName) {
                try {
                    $tableExists = DB::select("SHOW TABLES LIKE '{$tableName}'");
                    if (!empty($tableExists)) {
                        DB::table($tableName)->insert($notificationData);
                        break;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

        } catch (\Exception $e) {
            Log::warning('Failed to send dosen notification: ' . $e->getMessage());
        }
    }
}
