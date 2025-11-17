<?php

namespace App\Http\Controllers\API\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DosenProfileController extends Controller
{
    public function update(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'skill_ids' => 'array',
                'skill_ids.*' => 'exists:m_skill,skill_id',
                'minat_ids' => 'array',
                'minat_ids.*' => 'exists:m_minat,minat_id'
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();

            // Update user name
            $authUser = Auth::user();
            $user = User::where('id_user', $authUser->id_user)->first();
            if (!$user) {
                throw new \Exception('User tidak ditemukan');
            }
            $user->name = $request->name;
            $user->save();

            // Get dosen record
            $dosen = Dosen::where('user_id', $user->id_user)->first();
            if (!$dosen) {
                throw new \Exception('Data dosen tidak ditemukan');
            }

            // Update skills
            if ($request->has('skill_ids')) {
                // Delete existing skills
                DB::table('t_skill_dosen')
                    ->where('dosen_id', $dosen->id_dosen)
                    ->delete();

                // Insert new skills
                $skills = array_map(function($skillId) use ($dosen) {
                    return [
                        'dosen_id' => $dosen->id_dosen,
                        'skill_id' => $skillId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $request->skill_ids);

                DB::table('t_skill_dosen')->insert($skills);
            }

            // Update minat
            if ($request->has('minat_ids')) {
                // Delete existing minat
                DB::table('t_minat_dosen')
                    ->where('dosen_id', $dosen->id_dosen)
                    ->delete();

                // Insert new minat
                $minat = array_map(function($minatId) use ($dosen) {
                    return [
                        'dosen_id' => $dosen->id_dosen,
                        'minat_id' => $minatId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $request->minat_ids);

                DB::table('t_minat_dosen')->insert($minat);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating dosen profile: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCurrentDosen()
    {
        try {
            $user = Auth::user();
            $dosen = Dosen::where('user_id', $user->id_user)
                ->with(['wilayah', 'minat'])
                ->first();

            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $dosen
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting current dosen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get dosen data'
            ], 500);
        }
    }
}
