<?php
// filepath: d:\laragon\www\JTIintern\app\Http\Controllers\API\AdminController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        try {
            Log::info('Fetching admin users data');
            
            $admins = DB::table('m_user')
                ->where('role', 'admin')
                ->select('id_user', 'name', 'email', 'created_at')
                ->orderBy('name', 'asc')
                ->get();

            Log::info('Retrieved ' . count($admins) . ' admin users');
            
            return response()->json([
                'success' => true,
                'data' => $admins,
                'message' => 'Data admin berhasil ditemukan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching admin users: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $admin = DB::table('m_user')
                ->where('id_user', $id)
                ->where('role', 'admin')
                ->select('id_user', 'name', 'email', 'created_at', 'updated_at')
                ->first();

            if (!$admin) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Admin tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true, 
                'data' => $admin,
                'message' => 'Data admin berhasil ditemukan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting admin details: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:m_user,email',
                'password' => 'required|string|min:6'
            ], [
                'name.required' => 'Nama admin wajib diisi',
                'email.required' => 'Email admin wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 6 karakter'
            ]);

            $id = DB::table('m_user')->insertGetId([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'admin',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            Log::info('New admin created with ID: ' . $id);
            
            return response()->json([
                'success' => true, 
                'id_user' => $id,
                'message' => 'Admin berhasil ditambahkan'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->errors(),
                'validation_errors' => true
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating admin: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:m_user,email,' . $id . ',id_user',
                'password' => 'nullable|string|min:6'
            ], [
                'name.required' => 'Nama admin wajib diisi',
                'email.required' => 'Email admin wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'password.min' => 'Password minimal 6 karakter'
            ]);

            $data = [
                'name'     => $request->name,
                'email'    => $request->email,
                'updated_at' => now()
            ];
            
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Check if admin exists before updating
            $admin = DB::table('m_user')
                ->where('id_user', $id)
                ->where('role', 'admin')
                ->first();
                
            if (!$admin) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Admin tidak ditemukan'
                ], 404);
            }

            DB::table('m_user')
                ->where('id_user', $id)
                ->where('role', 'admin')
                ->update($data);
                
            Log::info('Admin updated with ID: ' . $id);

            return response()->json([
                'success' => true,
                'message' => 'Data admin berhasil diperbarui'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->errors(),
                'validation_errors' => true
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating admin: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Check if this is the last admin account to prevent deletion of all admin access
            $adminCount = DB::table('m_user')
                ->where('role', 'admin')
                ->count();
                
            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Tidak dapat menghapus admin terakhir'
                ], 400);
            }
            
            $deleted = DB::table('m_user')
                ->where('id_user', $id)
                ->where('role', 'admin')
                ->delete();

            if ($deleted) {
                Log::info('Admin deleted with ID: ' . $id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Admin berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'success' => false, 
                    'message' => 'Admin tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting admin: ' . $e->getMessage());
            
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}