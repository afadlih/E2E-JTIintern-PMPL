<?php

namespace App\Http\Controllers\API;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MahasiswaController extends Controller
{
    /**
     * ✅ SIMPLIFIED: Get all mahasiswa with filters
     */
    public function index(Request $request)
    {
        try {
            $query = DB::table('m_mahasiswa as m')
                ->leftJoin('m_user as u', 'm.id_user', '=', 'u.id_user')
                ->leftJoin('m_kelas as k', 'm.id_kelas', '=', 'k.id_kelas')
                ->leftJoin('m_magang as mg', 'm.id_mahasiswa', '=', 'mg.id_mahasiswa')
                ->select(
                    'm.id_mahasiswa',
                    'u.name',
                    'u.email',
                    'm.nim',
                    'm.alamat',
                    'm.ipk',
                    'k.id_kelas',
                    'k.nama_kelas',
                    DB::raw('CASE 
                        WHEN mg.id_mahasiswa IS NOT NULL THEN "Sedang Magang"
                        ELSE "Belum Magang"
                    END as status_magang')
                );

            // Apply filters
            if ($request->filled('kelas')) {
                $query->where('m.id_kelas', '=', $request->kelas);
            }

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('u.name', 'like', "%{$searchTerm}%")
                      ->orWhere('m.nim', 'like', "%{$searchTerm}%")
                      ->orWhere('u.email', 'like', "%{$searchTerm}%");
                });
            }

            $mahasiswa = $query->orderBy('u.name')->get();

            return response()->json([
                'success' => true,
                'data' => $mahasiswa
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching mahasiswa data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ SIMPLIFIED: Store new mahasiswa (no skills/minat - will be filled by student)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:m_user,email',
            'password' => 'required|string|min:6',
            'nim' => 'required|string|unique:m_mahasiswa,nim',
            'id_kelas' => 'required|exists:m_kelas,id_kelas',
            'alamat' => 'required|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'mahasiswa'
            ]);

            // Create mahasiswa
            $mahasiswa = Mahasiswa::create([
                'nim' => $request->nim,
                'id_user' => $user->id_user,
                'id_kelas' => $request->id_kelas,
                'alamat' => $request->alamat,
                'ipk' => null, // Will be filled by student
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Mahasiswa berhasil ditambahkan.',
                'data' => [
                    'user' => [
                        'id_user' => $user->id_user,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role
                    ],
                    'mahasiswa' => [
                        'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                        'nim' => $mahasiswa->nim,
                        'id_kelas' => $mahasiswa->id_kelas,
                        'alamat' => $mahasiswa->alamat,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating mahasiswa: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan data mahasiswa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Get specific mahasiswa with relations
     */
    public function show($id)
    {
        try {
            $mahasiswa = Mahasiswa::with(['user', 'kelas'])->findOrFail($id);

            // Get skills
            $skills = DB::table('t_skill_mahasiswa as sm')
                ->join('m_skill as s', 'sm.skill_id', '=', 's.skill_id')
                ->where('sm.user_id', $mahasiswa->id_user)
                ->select('s.skill_id', 's.nama', 'sm.lama_skill')
                ->get();

            // Get minat
            $minat = DB::table('t_minat_mahasiswa as tm')
                ->join('m_minat as m', 'tm.minat_id', '=', 'm.minat_id')
                ->where('tm.mahasiswa_id', $mahasiswa->id_mahasiswa)
                ->select('m.minat_id', 'm.nama_minat', 'm.deskripsi')
                ->get();

            // Get magang status
            $magang = DB::table('m_magang')
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                    'name' => $mahasiswa->user->name,
                    'email' => $mahasiswa->user->email,
                    'nim' => $mahasiswa->nim,
                    'id_kelas' => $mahasiswa->id_kelas,
                    'nama_kelas' => $mahasiswa->kelas->nama_kelas,
                    'alamat' => $mahasiswa->alamat,
                    'ipk' => $mahasiswa->ipk,
                    'status_magang' => $magang ? 'Sedang Magang' : 'Belum Magang',
                    'skills' => $skills,
                    'minat' => $minat,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching mahasiswa detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * ✅ Update mahasiswa data
     */
    public function update(Request $request, $id)
    {
        try {
            $mahasiswa = Mahasiswa::with('user')->findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'id_kelas' => 'required|exists:m_kelas,id_kelas',
                'alamat' => 'required|string|max:255',
                'nim' => 'required|string|max:15',
                'ipk' => 'nullable|numeric|min:0|max:4',
                'minat' => 'nullable|array',
                'minat.*' => 'exists:m_minat,minat_id',
                'skills' => 'nullable|array',
                'skills.*' => 'exists:m_skill,skill_id',
                'lama_skill' => 'nullable|integer|min:0'
            ]);

            DB::beginTransaction();

            // Update mahasiswa
            $mahasiswa->update([
                'id_kelas' => $validatedData['id_kelas'],
                'alamat' => $validatedData['alamat'],
                'nim' => $validatedData['nim'],
                'ipk' => $validatedData['ipk']
            ]);

            // Update user name
            $mahasiswa->user->update([
                'name' => $validatedData['name']
            ]);

            // Update minat
            if ($request->has('minat')) {
                DB::table('t_minat_mahasiswa')
                    ->where('mahasiswa_id', $mahasiswa->id_mahasiswa)
                    ->delete();

                foreach ($request->minat as $minatId) {
                    DB::table('t_minat_mahasiswa')->insert([
                        'mahasiswa_id' => $mahasiswa->id_mahasiswa,
                        'minat_id' => $minatId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Update skills
            if ($request->has('skills')) {
                DB::table('t_skill_mahasiswa')
                    ->where('user_id', $mahasiswa->id_user)
                    ->delete();

                foreach ($request->skills as $skillId) {
                    DB::table('t_skill_mahasiswa')->insert([
                        'user_id' => $mahasiswa->id_user,
                        'skill_id' => $skillId,
                        'lama_skill' => $request->lama_skill ?? 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data mahasiswa berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating mahasiswa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Delete mahasiswa
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $mahasiswa = Mahasiswa::findOrFail($id);

            // Delete related records
            DB::table('t_minat_mahasiswa')
                ->where('mahasiswa_id', $mahasiswa->id_mahasiswa)
                ->delete();

            DB::table('t_skill_mahasiswa')
                ->where('user_id', $mahasiswa->id_user)
                ->delete();

            // Delete mahasiswa (will cascade to user if configured)
            $mahasiswa->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data mahasiswa berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting mahasiswa: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ CONSOLIDATED: Import CSV (HAPUS method import() yang duplikat)
     */
    public function importCSV(Request $request)
    {
        try {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt|max:2048',
                'header_row' => 'boolean'
            ]);

            $file = $request->file('csv_file');
            $hasHeader = $request->boolean('header_row', true);

            // Read CSV
            $csvData = [];
            if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
                $rowIndex = 0;
                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    if ($hasHeader && $rowIndex === 0) {
                        $rowIndex++;
                        continue;
                    }
                    $csvData[] = $data;
                    $rowIndex++;
                }
                fclose($handle);
            }

            if (empty($csvData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File CSV kosong atau tidak valid'
                ], 400);
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($csvData as $index => $row) {
                try {
                    $rowNumber = $index + ($hasHeader ? 2 : 1);

                    // ✅ PERBAIKAN: Format CSV - nama,nim,alamat,ipk,nama_kelas,email (optional)
                    if (count($row) < 5) {
                        $errors[] = "Baris {$rowNumber}: Data tidak lengkap (minimal: nama,nim,alamat,ipk,nama_kelas)";
                        $errorCount++;
                        continue;
                    }

                    $nama = trim($row[0]);
                    $nim = trim($row[1]);
                    $alamat = trim($row[2]);
                    $ipk = !empty(trim($row[3])) ? floatval(trim($row[3])) : null;
                    $nama_kelas = trim($row[4]);
                    
                    // ✅ PERBAIKAN: Email handling yang lebih baik
                    $email = '';
                    if (isset($row[5]) && !empty(trim($row[5]))) {
                        $email = trim($row[5]);
                    } else {
                        $email = $nim . '@student.polinema.ac.id';
                    }

                    // ✅ PERBAIKAN: Validation yang lebih ketat
                    if (empty($nama)) {
                        $errors[] = "Baris {$rowNumber}: Nama tidak boleh kosong";
                        $errorCount++;
                        continue;
                    }

                    if (empty($nim)) {
                        $errors[] = "Baris {$rowNumber}: NIM tidak boleh kosong";
                        $errorCount++;
                        continue;
                    }

                    // ✅ PERBAIKAN: Validate NIM format (harus numeric)
                    if (!is_numeric($nim)) {
                        $errors[] = "Baris {$rowNumber}: NIM harus berupa angka";
                        $errorCount++;
                        continue;
                    }

                    if (empty($alamat)) {
                        $errors[] = "Baris {$rowNumber}: Alamat tidak boleh kosong";
                        $errorCount++;
                        continue;
                    }

                    if (empty($nama_kelas)) {
                        $errors[] = "Baris {$rowNumber}: Nama kelas tidak boleh kosong";
                        $errorCount++;
                        continue;
                    }

                    // ✅ PERBAIKAN: Validate email format
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Baris {$rowNumber}: Format email tidak valid ({$email})";
                        $errorCount++;
                        continue;
                    }

                    // Check duplicates
                    if (DB::table('m_mahasiswa')->where('nim', $nim)->exists()) {
                        $errors[] = "Baris {$rowNumber}: NIM {$nim} sudah terdaftar";
                        $errorCount++;
                        continue;
                    }

                    if (DB::table('m_user')->where('email', $email)->exists()) {
                        $errors[] = "Baris {$rowNumber}: Email {$email} sudah terdaftar";
                        $errorCount++;
                        continue;
                    }

                    // Get kelas
                    $kelas = DB::table('m_kelas')->where('nama_kelas', $nama_kelas)->first();
                    if (!$kelas) {
                        $errors[] = "Baris {$rowNumber}: Kelas '{$nama_kelas}' tidak ditemukan";
                        $errorCount++;
                        continue;
                    }

                    // Validate IPK
                    if ($ipk !== null && ($ipk < 0 || $ipk > 4)) {
                        $errors[] = "Baris {$rowNumber}: IPK harus antara 0.00 - 4.00 (nilai: {$ipk})";
                        $errorCount++;
                        continue;
                    }

                    // Create user
                    $user = User::create([
                        'name' => $nama,
                        'email' => $email,
                        'password' => Hash::make($nim),
                        'role' => 'mahasiswa'
                    ]);

                    // Create mahasiswa
                    Mahasiswa::create([
                        'id_user' => $user->id_user,
                        'nim' => intval($nim), // ✅ Convert to integer
                        'id_kelas' => $kelas->id_kelas,
                        'alamat' => $alamat,
                        'ipk' => $ipk,
                    ]);

                    $successCount++;

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Baris {$rowNumber}: Error sistem - {$e->getMessage()}";
                    Log::error("Import CSV error on row {$rowNumber}: " . $e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Import selesai. {$successCount} data berhasil diimpor" . 
                            ($errorCount > 0 ? ", {$errorCount} data gagal" : ""),
                'data' => [
                    'success_count' => $successCount,
                    'error_count' => $errorCount,
                    'total_processed' => $successCount + $errorCount
                ],
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import CSV error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Download CSV template
     */
    public function downloadTemplate()
    {
        try {
            $kelas = DB::table('m_kelas')->first();
            $namaKelasContoh = $kelas ? $kelas->nama_kelas : 'TI-3A';

            $csvContent = "nama,nim,alamat,ipk,nama_kelas,email\n";
            $csvContent .= "Muhammad Ahmad,2341720001,Jl. Contoh No. 123 Malang,3.50,{$namaKelasContoh},2341720001@student.polinema.ac.id\n";
            $csvContent .= "Siti Nurhaliza,2341720002,Jl. Merdeka No. 456 Blitar,3.75,{$namaKelasContoh},2341720002@student.polinema.ac.id\n";
            $csvContent .= "Budi Santoso,2341720003,Jl. Veteran No. 789 Surabaya,3.25,{$namaKelasContoh},\n";

            return response($csvContent, 200, [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="template_mahasiswa.csv"',
            ]);

        } catch (\Exception $e) {
            Log::error('Download template error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Export PDF
     */
    public function exportPDF(Request $request)
    {
        try {
            Log::info('Export PDF request:', [
                'filters' => $request->all(),
                'user' => auth()->user()->name ?? 'Unknown'
            ]);

            $query = DB::table('m_mahasiswa as m')
                ->leftJoin('m_user as u', 'm.id_user', '=', 'u.id_user')
                ->leftJoin('m_kelas as k', 'm.id_kelas', '=', 'k.id_kelas')
                ->leftJoin('m_magang as mg', 'm.id_mahasiswa', '=', 'mg.id_mahasiswa')
                ->select([
                    'u.name',
                    'm.nim',
                    'k.nama_kelas',
                    'm.alamat',
                    'm.ipk',
                    'u.email',
                    DB::raw('CASE 
                        WHEN mg.id_mahasiswa IS NOT NULL THEN "Sedang Magang"
                        ELSE "Belum Magang"
                    END as status_magang')
                ]);

            // Apply filters
            if ($request->filled('kelas')) {
                $query->where('m.id_kelas', '=', $request->kelas);
            }

            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('u.name', 'like', "%{$searchTerm}%")
                      ->orWhere('m.nim', 'like', "%{$searchTerm}%")
                      ->orWhere('u.email', 'like', "%{$searchTerm}%");
                });
            }

            $mahasiswa = $query->orderBy('u.name')->get();
            
            if ($mahasiswa->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data mahasiswa untuk diekspor'
                ], 404);
            }

            $timestamp = Carbon::now()->format('d-m-Y_H-i-s');

            // ✅ Create view content directly (fallback jika file view tidak ada)
            $html = $this->generatePDFContent($mahasiswa, $request, Carbon::now()->format('d F Y H:i:s'));

            $pdf = Pdf::loadHTML($html);
            $pdf->setPaper('a4', 'landscape');
            
            Log::info('PDF generated successfully', [
                'filename' => "data_mahasiswa_{$timestamp}.pdf",
                'total_records' => $mahasiswa->count()
            ]);

            return $pdf->download("data_mahasiswa_{$timestamp}.pdf");
            
        } catch (\Exception $e) {
            Log::error('Error exporting PDF: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengeksport PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Generate PDF HTML content
     */
    private function generatePDFContent($mahasiswa, $request, $timestamp)
    {
        $totalRecords = $mahasiswa->count();
        $filterInfo = '';
        
        if ($request->filled('kelas') || $request->filled('search')) {
            $filterInfo = '<p><strong>Filter Diterapkan:</strong> ';
            if ($request->filled('kelas')) {
                $filterInfo .= 'Kelas: ' . $request->kelas;
            }
            if ($request->filled('search')) {
                if ($request->filled('kelas')) $filterInfo .= ' | ';
                $filterInfo .= 'Pencarian: "' . $request->search . '"';
            }
            $filterInfo .= '</p>';
        }

        $tableRows = '';
        foreach ($mahasiswa as $index => $mhs) {
            $statusClass = $mhs->status_magang == 'Sedang Magang' ? 'status-magang' : 'status-belum';
            $tableRows .= '
                <tr>
                    <td class="text-center">' . ($index + 1) . '</td>
                    <td>' . ($mhs->name ?: '-') . '</td>
                    <td class="text-center">' . ($mhs->nim ?: '-') . '</td>
                    <td class="text-center">' . ($mhs->nama_kelas ?: '-') . '</td>
                    <td>' . ($mhs->alamat ?: '-') . '</td>
                    <td class="text-center">' . ($mhs->ipk ?: '-') . '</td>
                    <td class="text-center">
                        <span class="' . $statusClass . '">' . ($mhs->status_magang ?: 'Belum Magang') . '</span>
                    </td>
                </tr>';
        }

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Data Mahasiswa</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 20px; }
                .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .header h2 { margin: 0; padding: 0; color: #333; }
                .header p { margin: 5px 0; color: #666; }
                .info { margin-bottom: 15px; background-color: #f8f9fa; padding: 10px; border-radius: 5px; }
                .info p { margin: 5px 0; }
                table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                th, td { border: 1px solid #333; padding: 8px; text-align: left; vertical-align: top; }
                th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
                .text-center { text-align: center; }
                .status-belum { color: #666; background-color: #f8f9fa; padding: 2px 6px; border-radius: 3px; }
                .status-magang { color: #28a745; background-color: #d4edda; padding: 2px 6px; border-radius: 3px; font-weight: bold; }
                .footer { margin-top: 20px; font-size: 10px; color: #666; text-align: center; border-top: 1px solid #dee2e6; padding-top: 10px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>DATA MAHASISWA</h2>
                <p>Politeknik Negeri Malang - Jurusan Teknologi Informasi</p>
            </div>

            <div class="info">
                <p><strong>Tanggal Export:</strong> ' . $timestamp . '</p>
                <p><strong>Total Data:</strong> ' . $totalRecords . ' mahasiswa</p>
                ' . $filterInfo . '
            </div>

            <table>
                <thead>
                    <tr>
                        <th width="8%">No</th>
                        <th width="25%">Nama</th>
                        <th width="15%">NIM</th>
                        <th width="12%">Kelas</th>
                        <th width="20%">Alamat</th>
                        <th width="8%">IPK</th>
                        <th width="12%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $tableRows . '
                </tbody>
            </table>

            <div class="footer">
                <p>Dokumen ini digenerate secara otomatis pada ' . $timestamp . '</p>
                <p>© ' . date('Y') . ' Politeknik Negeri Malang - Sistem Informasi Magang</p>
            </div>
        </body>
        </html>';
    }
}
