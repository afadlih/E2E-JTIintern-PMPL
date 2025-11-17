<?php
// filepath: d:\laragon\www\JTIintern\app\Http\Controllers\SkillController.php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SkillController extends Controller
{
    /**
     * Display the skill management page
     */
    public function index()
    {
        // Just return the view, no data passing
        return view('pages.skill');
    }

    /**
     * API endpoint to get all skills
     */
    public function getSkills()
    {
        try {
            $skills = Skill::orderBy('nama', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => $skills
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching skills: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data skill'
            ], 500);
        }
    }

    /**
     * API endpoint to store a new skill
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255|unique:m_skill,nama',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $skill = Skill::create(['nama' => $request->nama]);

            return response()->json([
                'success' => true,
                'message' => 'Skill berhasil ditambahkan',
                'data' => $skill
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating skill: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menambahkan skill'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255|unique:m_skill,nama,' . $id . ',skill_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $skill = Skill::findOrFail($id);
            $skill->update([
                'nama' => $request->nama,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Skill berhasil diperbarui',
                'data' => $skill
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating skill: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui skill'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $skill = Skill::findOrFail($id);
            $skill->delete();

            return response()->json([
                'success' => true,
                'message' => 'Skill berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting skill: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus skill'
            ], 500);
        }
    }
}
