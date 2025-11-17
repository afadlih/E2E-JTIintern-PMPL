<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Lamaran;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Magang;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Exception;


class DosenController extends Controller
{
    public function index()
    {
        $dosen = Dosen::with(['user'])->get();
        $data = $dosen->map(function ($item) {
            return [
                'id_dosen' => $item->id_dosen,
                'nama_dosen' => $item->user->name ?? '-',
                'email' => $item->user->email ?? '-',
                'nip' => $item->nip ?? '-',
            ];
        });
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_dosen' => 'required|string|max:255',
            'nip' => 'required|unique:m_dosen,nip',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:m_skill,skill_id',
            'minat' => 'nullable|array',
            'minat.*' => 'exists:m_minat,minat_id',
        ]);

        DB::beginTransaction();
        try {
            // Email dan password sama dengan NIP
            $nip = $request->nip;
            $email = $nip . '@gmail.com';

            // Buat user baru
            $user = User::create([
                'name' => $request->nama_dosen,
                'email' => $email,
                'password' => bcrypt($nip),
                'role' => 'dosen'
            ]);

            // Buat dosen baru (without wilayah_id)
            $dosen = Dosen::create([
                'user_id' => $user->id_user,
                'nip' => $nip
            ]);

            // Simpan skills jika ada
            if ($request->has('skills') && is_array($request->skills)) {
                foreach ($request->skills as $skillId) {
                    DB::table('t_skill_dosen')->insert([
                        'id_dosen' => $dosen->id_dosen, // CHANGE HERE: dosen_id → id_dosen
                        'skill_id' => $skillId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Similarly for minat
            if ($request->has('minat') && is_array($request->minat)) {
                foreach ($request->minat as $minatId) {
                    DB::table('t_minat_dosen')->insert([
                        'dosen_id' => $dosen->id_dosen, // FIXED: Use dosen_id instead of id_dosen
                        'minat_id' => $minatId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating dosen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $dosen = Dosen::with(['user'])->findOrFail($id);

        // Get skills for this dosen - Change dosen_id to id_dosen
        $skills = DB::table('t_skill_dosen as sd')
            ->join('m_skill as s', 's.skill_id', '=', 'sd.skill_id')
            ->where('sd.id_dosen', $dosen->id_dosen) // CHANGE HERE: dosen_id → id_dosen
            ->select('s.skill_id', 's.nama')
            ->get();

        // Get minat for this dosen - Change dosen_id to id_dosen
        $minat = DB::table('t_minat_dosen as md')
            ->join('m_minat as m', 'm.minat_id', '=', 'md.minat_id')
            ->where('md.dosen_id', $dosen->id_dosen) // FIXED: Use dosen_id instead of id_dosen
            ->select('m.minat_id', 'm.nama_minat')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'id_dosen' => $dosen->id_dosen,
                'nama_dosen' => $dosen->user->name ?? '-',
                'email' => $dosen->user->email ?? '-',
                'nip' => $dosen->nip ?? '-',
                'skills' => $skills,
                'minat' => $minat
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_dosen' => 'required|string|max:255',
            'nip' => 'required|unique:m_dosen,nip,' . $id . ',id_dosen',
            'skills' => 'nullable|array',
            'skills.*' => 'exists:m_skill,skill_id',
            'minat' => 'nullable|array',
            'minat.*' => 'exists:m_minat,minat_id',
        ]);

        DB::beginTransaction();
        try {
            $dosen = Dosen::findOrFail($id);
            $user = $dosen->user;

            // Update user (nama)
            $user->name = $request->nama_dosen;
            $user->save();

            // Update dosen (without wilayah_id)
            // $dosen->wilayah_id = $request->wilayah_id; - REMOVED
            $dosen->nip = $request->nip;
            $dosen->save();

            // Update skills
            if ($request->has('skills')) {
                // Delete existing skills
                DB::table('t_skill_dosen')->where('id_dosen', $id)->delete(); // FIXED: Delete from correct table
                // Insert new skills
                foreach ($request->skills as $skillId) {
                    DB::table('t_skill_dosen')->insert([
                        'id_dosen' => $dosen->id_dosen, // CHANGE HERE: dosen_id → id_dosen
                        'skill_id' => $skillId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Update minat
            if ($request->has('minat')) {
                DB::table('t_minat_dosen')->where('dosen_id', $id)->delete(); // FIXED: Use dosen_id instead of id_dosen
                // Insert new minat
                foreach ($request->minat as $minatId) {
                    DB::table('t_minat_dosen')->insert([
                        'dosen_id' => $dosen->id_dosen, // FIXED: Use dosen_id instead of id_dosen
                        'minat_id' => $minatId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating dosen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $dosen = Dosen::findOrFail($id);
            $user = $dosen->user;

            // Delete related skill records
            DB::table('t_skill_dosen')->where('id_dosen', $id)->delete(); // CHANGE HERE: dosen_id → id_dosen

            // Delete related minat records
            DB::table('t_minat_dosen')->where('dosen_id', $id)->delete(); // FIXED: Use dosen_id instead of id_dosen

            // Delete dosen and user
            $dosen->delete();
            if ($user) $user->delete();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting dosen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function withPerusahaan()
    {
        try {
            // Get all dosen with their user data (removed wilayah join)
            $dosen = DB::table('m_dosen')
                ->join('m_user', 'm_dosen.user_id', '=', 'm_user.id_user') // FIXED: user_id instead of id_user
                ->select(
                    'm_dosen.*',
                    'm_user.name',
                    'm_user.email'
                )
                ->get();

            // For each dosen, get their current assignments from BOTH t_lamaran AND m_magang
            foreach ($dosen as $d) {
                // 1. Get assigned students from t_lamaran (pending applications with dosen assigned)
                $pendingAssignments = DB::table('t_lamaran')
                    ->join('m_mahasiswa', 't_lamaran.id_mahasiswa', '=', 'm_mahasiswa.id_mahasiswa')
                    ->join('m_user', 'm_mahasiswa.id_user', '=', 'm_user.id_user')
                    ->join('m_lowongan', 't_lamaran.id_lowongan', '=', 'm_lowongan.id_lowongan')
                    ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                    ->where('t_lamaran.id_dosen', $d->id_dosen)
                    ->select(
                        't_lamaran.id_lamaran',
                        'm_mahasiswa.id_mahasiswa',
                        'm_user.name as mahasiswa_name',
                        'm_mahasiswa.nim',
                        'm_lowongan.judul_lowongan',
                        'm_perusahaan.nama_perusahaan',
                        DB::raw("'pending' as status")
                    )
                    ->get();

                // 2. Get assigned students from m_magang (active internships)
                $activeAssignments = DB::table('m_magang')
                    ->join('m_mahasiswa', 'm_magang.id_mahasiswa', '=', 'm_mahasiswa.id_mahasiswa')
                    ->join('m_user', 'm_mahasiswa.id_user', '=', 'm_user.id_user')
                    ->join('m_lowongan', 'm_magang.id_lowongan', '=', 'm_lowongan.id_lowongan')
                    ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                    ->where('m_magang.id_dosen', $d->id_dosen)
                    ->select(
                        'm_magang.id_magang',
                        'm_mahasiswa.id_mahasiswa',
                        'm_user.name as mahasiswa_name',
                        'm_mahasiswa.nim',
                        'm_lowongan.judul_lowongan',
                        'm_perusahaan.nama_perusahaan',
                        'm_magang.status'
                    )
                    ->get();

                // Combine both result sets
                $allAssignments = $pendingAssignments->concat($activeAssignments);
                
                $d->magangBimbingan = $allAssignments;
                
                // Get dosen skills
                $skills = DB::table('t_skill_dosen')
                    ->join('m_skill', 't_skill_dosen.skill_id', '=', 'm_skill.skill_id')
                    ->where('t_skill_dosen.id_dosen', $d->id_dosen)
                    ->select('m_skill.nama as nama_skill', 'm_skill.skill_id')
                    ->get();
                    
                $d->skills = $skills->map(function($skill) {
                    return ['skill' => $skill];
                });
                
                // Log for debugging
                Log::info("Dosen {$d->id_dosen} has " . $allAssignments->count() . " total assignments");
            }

            return response()->json([
                'success' => true,
                'data' => $dosen
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching dosen with perusahaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dosen data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function withDetails()
    {
        try {
            // Get dosen with relevant relationships (removed wilayah)
            $dosens = Dosen::with([
                'user',
                'skills.skill'
            ])->get();

            $dosens = $dosens->map(function ($dosen) {
                try {
                    // Load magangBimbingan relationship manually
                    $bimbingan = Magang::where('id_dosen', $dosen->id_dosen)
                        ->where(function ($query) {
                            $query->where('status', '!=', 'ditolak')
                                ->orWhereNull('status');
                        })
                        ->get(['id_magang', 'id_mahasiswa', 'id_lowongan', 'status', 'created_at']);

                    // Create consistent property names
                    $dosen->magangBimbingan = $bimbingan;
                    $dosen->magang_bimbingan = $bimbingan;
                } catch (\Exception $e) {
                    Log::error('Error loading magangBimbingan: ' . $e->getMessage());
                    $dosen->magangBimbingan = [];
                    $dosen->magang_bimbingan = [];
                }

                return $dosen;
            });

            return response()->json([
                'success' => true,
                'data' => $dosens,
                'timestamp' => now()->timestamp
            ]);
        } catch (\Exception $e) {
            Log::error('Error in withDetails: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeAssignments($id)
    {
        try {
            // Log the request for debugging
            Log::info('Removing assignments for dosen ID: ' . $id);

            // Count for tracking how many assignments were removed
            $count = 0;

            // Remove from Lamaran table (pending applications)
            $lamaranCount = Lamaran::where('id_dosen', $id)->update(['id_dosen' => null]);
            $count += $lamaranCount;
            Log::info('Removed ' . $lamaranCount . ' assignments from Lamaran table for dosen ID: ' . $id);

            // Also remove from Magang table (active internships)
            $magangCount = Magang::where('id_dosen', $id)->update(['id_dosen' => null]);
            $count += $magangCount;
            Log::info('Removed ' . $magangCount . ' assignments from Magang table for dosen ID: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Assignments removed successfully',
                'count' => $count,
                'details' => [
                    'lamaran' => $lamaranCount,
                    'magang' => $magangCount
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing assignments: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove assignments: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignMahasiswa(Request $request, $dosenId)
    {
        try {
            $request->validate([
                'magang_ids' => 'required|array',
                'magang_ids.*' => 'required|integer'
            ]);

            $count = 0;
            foreach ($request->magang_ids as $lamaranId) {
                // Update t_lamaran first (new workflow)
                $updated = DB::table('t_lamaran')
                    ->where('id_lamaran', $lamaranId)
                    ->update([
                        'id_dosen' => $dosenId,
                        'updated_at' => now()
                    ]);
                    
                if ($updated) {
                    $count++;
                    Log::info("Assigned dosen ID {$dosenId} to lamaran ID {$lamaranId}");
                } else {
                    // Legacy support - check if it's in m_magang (old workflow)
                    $updated = DB::table('m_magang')
                        ->where('id_magang', $lamaranId)
                        ->update([
                            'id_dosen' => $dosenId,
                            'updated_at' => now()
                        ]);
                        
                    if ($updated) {
                        $count++;
                        Log::info("Assigned dosen ID {$dosenId} to magang ID {$lamaranId}");
                    }
                }
            }

            if ($count > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Berhasil menetapkan {$count} mahasiswa ke dosen",
                    'count' => $count
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada mahasiswa yang berhasil ditetapkan ke dosen'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error assigning mahasiswa to dosen: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menetapkan mahasiswa ke dosen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function import(Request $request)
    {
        // Validate request
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            $content = file_get_contents($path);

            // Detect delimiter (comma or semicolon)
            $delimiter = ',';
            if (strpos($content, ';') !== false) {
                $delimiter = ';';
            }

            $file = fopen($path, 'r');

            // Handle UTF-8 BOM
            $firstRow = fgets($file, 4);
            if (strpos($firstRow, "\xEF\xBB\xBF") === 0) {
                rewind($file);
                fread($file, 3);
            } else {
                rewind($file);
            }

            // Read header
            $header = fgetcsv($file, 0, $delimiter);

            if (!$header) {
                fclose($file);
                return response()->json([
                    'success' => false,
                    'message' => 'Format file tidak valid atau file kosong'
                ], 400);
            }

            // Map headers to expected field names (case insensitive)
            $header = array_map('strtolower', array_map('trim', $header));

            // Define header mappings (removed wilayah mappings)
            $headerMap = [
                'nama' => 'nama_dosen',
                'nama dosen' => 'nama_dosen',
                'nama_dosen' => 'nama_dosen',
                'nip' => 'nip'
            ];

            // Map column indices
            $columnMap = [];
            foreach ($header as $index => $columnName) {
                if (isset($headerMap[$columnName])) {
                    $fieldName = $headerMap[$columnName];
                    $columnMap[$fieldName] = $index;
                }
            }

            // Verify required fields (removed wilayah requirement)
            $requiredFields = ['nama_dosen', 'nip'];
            $missingColumns = [];

            foreach ($requiredFields as $field) {
                if (!isset($columnMap[$field])) {
                    $missingColumns[] = $field;
                }
            }

            if (count($missingColumns) > 0) {
                fclose($file);
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak memiliki kolom wajib: ' . implode(', ', $missingColumns)
                ], 400);
            }

            $imported = 0;
            $errors = [];
            $rowNumber = 1; // Start with row 1 (after header)

            DB::beginTransaction();

            while (($row = fgetcsv($file, 0, $delimiter)) !== false) {
                $rowNumber++;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    // Extract data
                    $data = [];
                    $data['nama_dosen'] = isset($columnMap['nama_dosen']) && isset($row[$columnMap['nama_dosen']])
                        ? trim($row[$columnMap['nama_dosen']]) : null;
                    $data['nip'] = isset($columnMap['nip']) && isset($row[$columnMap['nip']])
                        ? trim($row[$columnMap['nip']]) : null;

                    // Check if NIP already exists
                    $existingDosen = Dosen::where('nip', $data['nip'])->first();
                    if ($existingDosen) {
                        $errors[] = "Error pada baris {$rowNumber}: NIP '{$data['nip']}' sudah terdaftar";
                        continue;
                    }

                    // Generate email based on NIP (to match store method)
                    $email = $data['nip'] . '@gmail.com';

                    // Check if email already exists
                    $existingUser = User::where('email', $email)->first();
                    if ($existingUser) {
                        $errors[] = "Error pada baris {$rowNumber}: Email '{$email}' sudah digunakan";
                        continue;
                    }

                    // Create a user account for the dosen (using NIP as password)
                    $user = User::create([
                        'name' => $data['nama_dosen'],
                        'email' => $email,
                        'password' => bcrypt($data['nip']), // Use NIP as password to match store method
                        'role' => 'dosen',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Create dosen record with user_id
                    Dosen::create([
                        'user_id' => $user->id_user, // Note: using id_user, not id
                        'nip' => $data['nip'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Error pada baris {$rowNumber}: " . $e->getMessage();
                }
            }

            fclose($file);

            if ($imported > 0) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Berhasil mengimpor {$imported} data dosen" . (count($errors) > 0 ? " (dengan " . count($errors) . " error)" : ""),
                    'imported' => $imported,
                    'errors' => $errors
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Tidak ada data yang berhasil diimpor",
                    'errors' => $errors
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengimpor data dosen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            // Get filtered data (removed wilayah join)
            $query = DB::table('m_dosen')
                ->leftJoin('m_user', 'm_dosen.user_id', '=', 'm_user.id_user')
                ->leftJoin(
                    DB::raw('(SELECT id_dosen, COUNT(*) as bimbingan_count FROM m_magang WHERE status != "ditolak" OR status IS NULL GROUP BY id_dosen) m'),
                    'm_dosen.id_dosen',
                    '=',
                    'm.id_dosen'
                )
                ->select(
                    'm_dosen.id_dosen',
                    'm_dosen.nip',
                    'm_user.name as nama_dosen',
                    'm_user.email',
                    DB::raw('COALESCE(m.bimbingan_count, 0) as jumlah_bimbingan')
                );

            $dosen = $query->orderBy('m_user.name')->get();

            // Get current timestamp
            $timestamp = Carbon::now()->format('d-m-Y_H-i-s');

            // Load the view for PDF
            $pdf = PDF::loadView('exports.dosen-pdf', [
                'dosen' => $dosen,
                'timestamp' => Carbon::now()->format('d F Y H:i:s'),
                'total' => $dosen->count()
            ]);

            // Set paper to landscape for better table viewing
            $pdf->setPaper('a4', 'landscape');

            // Return the PDF for download
            return $pdf->download("data_dosen_{$timestamp}.pdf");
        } catch (Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengeksport PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDosenWithPerusahaan()
    {
        try {
            // Get all dosen with their user data (removed wilayah join)
            $dosen = DB::table('m_dosen')
                ->join('m_user', 'm_dosen.user_id', '=', 'm_user.id_user') // FIXED: user_id instead of id_user
                ->select(
                    'm_dosen.*',
                    'm_user.name',
                    'm_user.email'
                )
                ->get();

            // For each dosen, get their current assignments from t_lamaran, not m_magang
            foreach ($dosen as $d) {
                // Get assigned students from t_lamaran
                $assignments = DB::table('t_lamaran')
                    ->join('m_mahasiswa', 't_lamaran.id_mahasiswa', '=', 'm_mahasiswa.id_mahasiswa')
                    ->join('m_user', 'm_mahasiswa.id_user', '=', 'm_user.id_user')
                    ->join('m_lowongan', 't_lamaran.id_lowongan', '=', 'm_lowongan.id_lowongan')
                    ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                    ->where('t_lamaran.id_dosen', $d->id_dosen)
                    ->select(
                        't_lamaran.id_lamaran',
                        'm_mahasiswa.id_mahasiswa',
                        'm_user.name as mahasiswa_name',
                        'm_mahasiswa.nim',
                        'm_lowongan.judul_lowongan',
                        'm_perusahaan.nama_perusahaan'
                    )
                    ->get();
                    
                $d->magangBimbingan = $assignments;
                
                // Get dosen skills
                $skills = DB::table('t_skill_dosen')
                    ->join('m_skill', 't_skill_dosen.skill_id', '=', 'm_skill.skill_id')
                    ->where('t_skill_dosen.id_dosen', $d->id_dosen)
                    ->select('m_skill.nama as nama_skill', 'm_skill.skill_id')
                    ->get();
                    
                $d->skills = $skills->map(function($skill) {
                    return ['skill' => $skill];
                });
            }

            return response()->json([
                'success' => true,
                'data' => $dosen
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching dosen with perusahaan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load dosen data: ' . $e->getMessage()
            ], 500);
        }
    }
}
