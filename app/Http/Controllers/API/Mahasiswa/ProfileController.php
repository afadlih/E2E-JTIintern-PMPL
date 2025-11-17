<?php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get skills data for profile management
     */
    public function getSkills()
    {
        try {
            $user = auth()->user();
            
            // Get all available skills
            $allSkills = DB::table('m_skill')
                ->select('skill_id', 'nama as nama_skill')
                ->orderBy('nama')
                ->get();

            // Get user's current skills
            $userSkills = DB::table('t_skill_mahasiswa')
                ->join('m_skill', 't_skill_mahasiswa.skill_id', '=', 'm_skill.skill_id')
                ->where('t_skill_mahasiswa.user_id', $user->id_user)
                ->select('m_skill.skill_id', 'm_skill.nama as nama_skill')
                ->get();

            return response()->json([
                'success' => true,
                'allSkills' => $allSkills,
                'userSkills' => $userSkills
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting skills: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load skills data'
            ], 500);
        }
    }

    /**
     * Update user skills
     */
    public function updateSkills(Request $request)
    {
        try {
            $user = auth()->user();
            $skills = $request->input('skills', []);

            // Delete existing skills
            DB::table('t_skill_mahasiswa')
                ->where('user_id', $user->id_user)
                ->delete();

            // Insert new skills
            if (!empty($skills)) {
                $skillData = [];
                foreach ($skills as $skillId) {
                    $skillData[] = [
                        'user_id' => $user->id_user,
                        'skill_id' => $skillId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                DB::table('t_skill_mahasiswa')->insert($skillData);
            }

            // Get updated skills for response
            $updatedSkills = DB::table('t_skill_mahasiswa')
                ->join('m_skill', 't_skill_mahasiswa.skill_id', '=', 'm_skill.skill_id')
                ->where('t_skill_mahasiswa.user_id', $user->id_user)
                ->select('m_skill.skill_id', 'm_skill.nama as nama_skill')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Skills updated successfully',
                'skills' => $updatedSkills
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating skills: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update skills'
            ], 500);
        }
    }

    /**
     * Get minat data for profile management
     */
    public function getMinat()
    {
        try {
            $user = auth()->user();
            
            // Get mahasiswa data
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa data not found'
                ], 404);
            }

            // Get all available minat
            $allMinat = DB::table('m_minat')
                ->select('minat_id', 'nama_minat')
                ->orderBy('nama_minat')
                ->get();

            // Get user's current minat
            $userMinat = DB::table('t_minat_mahasiswa')
                ->join('m_minat', 't_minat_mahasiswa.minat_id', '=', 'm_minat.minat_id')
                ->where('t_minat_mahasiswa.mahasiswa_id', $mahasiswa->id_mahasiswa)
                ->select('m_minat.minat_id', 'm_minat.nama_minat')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'all_minat' => $allMinat,
                    'user_minat' => $userMinat
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting minat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load minat data'
            ], 500);
        }
    }

    /**
     * Update user minat
     */
    public function updateMinat(Request $request)
    {
        try {
            $user = auth()->user();
            $minatIds = $request->input('minat', []);

            // Get mahasiswa data
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa data not found'
                ], 404);
            }

            // Delete existing minat
            DB::table('t_minat_mahasiswa')
                ->where('mahasiswa_id', $mahasiswa->id_mahasiswa)
                ->delete();

            // Insert new minat
            if (!empty($minatIds)) {
                $minatData = [];
                foreach ($minatIds as $minatId) {
                    $minatData[] = [
                        'mahasiswa_id' => $mahasiswa->id_mahasiswa,
                        'minat_id' => $minatId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                DB::table('t_minat_mahasiswa')->insert($minatData);
            }

            // Get updated minat for response
            $updatedMinat = DB::table('t_minat_mahasiswa')
                ->join('m_minat', 't_minat_mahasiswa.minat_id', '=', 'm_minat.minat_id')
                ->where('t_minat_mahasiswa.mahasiswa_id', $mahasiswa->id_mahasiswa)
                ->select('m_minat.minat_id', 'm_minat.nama_minat')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Minat updated successfully',
                'data' => [
                    'minat' => $updatedMinat
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating minat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update minat'
            ], 500);
        }
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        try {
            Log::info('🔄 Profile update request received:', [
                'user_id' => auth()->user()->id_user,
                'request_data' => $request->all(),
                'content_type' => $request->header('Content-Type')
            ]);

            // ✅ PERBAIKAN: Validasi dengan rules yang lebih tepat
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'telp' => 'nullable|string|max:15|regex:/^[0-9+\-\s]+$/',
                'ipk' => 'nullable|numeric|between:0,4.00',
                'alamat' => 'nullable|string|max:1000',
                'wilayah_id' => 'nullable|integer|exists:m_wilayah,wilayah_id',
            ], [
                'name.required' => 'Nama wajib diisi',
                'name.max' => 'Nama maksimal 255 karakter',
                'telp.max' => 'Nomor telepon maksimal 15 karakter',
                'telp.regex' => 'Format nomor telepon tidak valid',
                'ipk.numeric' => 'IPK harus berupa angka',
                'ipk.between' => 'IPK harus antara 0.00 - 4.00',
                'alamat.max' => 'Alamat maksimal 1000 karakter',
                'wilayah_id.exists' => 'Wilayah yang dipilih tidak valid',
                'wilayah_id.integer' => 'Wilayah ID harus berupa angka'
            ]);

            if ($validator->fails()) {
                Log::warning('❌ Profile update validation failed:', [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Data yang dimasukkan tidak valid',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            
            if (!$user) {
                Log::error('❌ No authenticated user found');
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 401);
            }
            
            DB::beginTransaction();

            try {
                // ✅ STEP 1: UPDATE USER DATA
                $userUpdateResult = DB::table('m_user')
                    ->where('id_user', $user->id_user)
                    ->update([
                        'name' => trim($request->name),
                        'updated_at' => now()
                    ]);

                Log::info('👤 User table update:', [
                    'user_id' => $user->id_user,
                    'affected_rows' => $userUpdateResult,
                    'new_name' => $request->name,
                    'sql_executed' => true
                ]);

                // ✅ STEP 2: GET OR CREATE MAHASISWA RECORD
                $mahasiswa = DB::table('m_mahasiswa')
                    ->where('id_user', $user->id_user)
                    ->first();

                Log::info('📋 Mahasiswa record check:', [
                    'user_id' => $user->id_user,
                    'mahasiswa_exists' => $mahasiswa ? true : false,
                    'mahasiswa_id' => $mahasiswa->id_mahasiswa ?? null
                ]);

                if (!$mahasiswa) {
                    // ✅ CREATE NEW MAHASISWA RECORD
                    $mahasiswaId = DB::table('m_mahasiswa')->insertGetId([
                        'id_user' => $user->id_user,
                        'nim' => null, // Will be set by admin
                        'telp' => $request->telp,
                        'ipk' => $request->ipk,
                        'alamat' => $request->alamat,
                        'wilayah_id' => $request->wilayah_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    Log::info('✅ Created new mahasiswa record:', [
                        'mahasiswa_id' => $mahasiswaId,
                        'user_id' => $user->id_user
                    ]);
                    
                    $mahasiswaUpdateResult = 1; // Consider as successful insert
                } else {
                    // ✅ UPDATE EXISTING MAHASISWA RECORD
                    $mahasiswaUpdateData = [
                        'telp' => $request->telp,
                        'ipk' => $request->ipk,
                        'alamat' => $request->alamat,
                        'wilayah_id' => $request->wilayah_id,
                        'updated_at' => now()
                    ];

                    $mahasiswaUpdateResult = DB::table('m_mahasiswa')
                        ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                        ->update($mahasiswaUpdateData);

                    Log::info('📋 Mahasiswa table update:', [
                        'mahasiswa_id' => $mahasiswa->id_mahasiswa,
                        'affected_rows' => $mahasiswaUpdateResult,
                        'update_data' => $mahasiswaUpdateData,
                        'sql_executed' => true
                    ]);
                }

                // ✅ COMMIT TRANSACTION
                DB::commit();
                
                Log::info('💾 Transaction committed successfully');

                // ✅ STEP 3: FETCH FRESH DATA
                $updatedUser = DB::table('m_user')
                    ->where('id_user', $user->id_user)
                    ->first();

                $updatedMahasiswa = DB::table('m_mahasiswa')
                    ->where('id_user', $user->id_user)
                    ->first();

                // ✅ STEP 4: VERIFY DATA WAS ACTUALLY UPDATED
                $verificationData = [
                    'user_name_updated' => $updatedUser->name === trim($request->name),
                    'mahasiswa_telp_updated' => $updatedMahasiswa->telp === $request->telp,
                    'mahasiswa_ipk_updated' => $updatedMahasiswa->ipk == $request->ipk,
                    'mahasiswa_alamat_updated' => $updatedMahasiswa->alamat === $request->alamat,
                    'mahasiswa_wilayah_updated' => $updatedMahasiswa->wilayah_id == $request->wilayah_id
                ];

                Log::info('✅ Data verification after update:', [
                    'user_id' => $user->id_user,
                    'verification' => $verificationData,
                    'user_update_count' => $userUpdateResult,
                    'mahasiswa_update_count' => $mahasiswaUpdateResult,
                    'fresh_user_data' => $updatedUser,
                    'fresh_mahasiswa_data' => $updatedMahasiswa
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Profil berhasil diperbarui',
                    'user' => $updatedUser,
                    'mahasiswa' => $updatedMahasiswa,
                    'debug' => [
                        'user_updated' => $userUpdateResult > 0,
                        'mahasiswa_updated' => $mahasiswaUpdateResult > 0,
                        'verification' => $verificationData
                    ]
                ]);

            } catch (\Exception $dbError) {
                DB::rollBack();
                
                Log::error('❌ Database error during profile update:', [
                    'user_id' => $user->id_user,
                    'error_message' => $dbError->getMessage(),
                    'error_code' => $dbError->getCode(),
                    'trace' => $dbError->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data ke database: ' . $dbError->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('❌ General error updating profile:', [
                'user_id' => auth()->user()->id_user ?? 'unknown',
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update avatar
     */
    public function updateAvatar(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            
            // Get mahasiswa data
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa data not found'
                ], 404);
            }

            // Delete old foto if exists
            if ($mahasiswa->foto) {
                Storage::disk('public')->delete($mahasiswa->foto);
            }

            // Store new foto
            $file = $request->file('foto');
            $filename = 'mahasiswa_' . $user->id_user . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('mahasiswa/foto', $filename, 'public');

            // Update database
            DB::table('m_mahasiswa')
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->update([
                    'foto' => $path,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Avatar updated successfully',
                'foto_url' => asset('storage/' . $path)
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating avatar: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update avatar'
            ], 500);
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();

            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            // Update password
            DB::table('m_user')
                ->where('id_user', $user->id_user)
                ->update([
                    'password' => Hash::make($request->password),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating password: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password'
            ], 500);
        }
    }

    public function uploadCv(Request $request)
{
    try {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_user', $user->id_user)->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ], 404);
        }

        if ($request->hasFile('cv')) {
            $file = $request->file('cv');
            
            // Validate file
            $validator = Validator::make(['cv' => $file], [
                'cv' => 'required|mimes:pdf|max:5120', // 5MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Delete old CV if exists
            if ($mahasiswa->cv) {
                Storage::delete('public/' . $mahasiswa->cv);
            }

            // Store new CV
            $path = $file->store('cv', 'public');
            
            // Update database
            $mahasiswa->cv = $path; // Use 'cv' instead of 'cv_path'
            $mahasiswa->cv_updated_at = now();
            $mahasiswa->save();

            return response()->json([
                'success' => true,
                'message' => 'CV berhasil diupload',
                'cv_url' => asset('storage/' . $path)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'File tidak ditemukan'
        ], 400);
    } catch (\Exception $e) {
        Log::error('Error uploading CV: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengupload CV'
        ], 500);
    }
}

public function deleteCv()
{
    try {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('id_user', $user->id_user)->first();
        
        if (!$mahasiswa || !$mahasiswa->cv) {
            return response()->json([
                'success' => false,
                'message' => 'CV tidak ditemukan'
            ], 404);
        }

        // Delete CV file
        Storage::disk('public')->delete($mahasiswa->cv);
        
        // Update database
        $mahasiswa->update([
            'cv' => null,
            'cv_updated_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'CV berhasil dihapus'
        ]);

    } catch (\Exception $e) {
        Log::error('Error deleting CV: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus CV'
        ], 500);
    }
}
}
