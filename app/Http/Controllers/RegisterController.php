<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function create()
    {
        try {
            return view('auth.register');
        } catch (\Exception $e) {
            Log::error('Register page error: ' . $e->getMessage());
            return response()->view('errors.500', [], 500);
        }
    }

    public function store()
    {
        // Log semua request data untuk debug
        Log::info('Registration POST received', [
            'all_data' => request()->all(),
            'method' => request()->method(),
            'url' => request()->url()
        ]);
        
        try {
            // Validasi
            $validated = request()->validate([
                'name' => 'required|string|max:255|min:2',
                'email' => 'required|email|max:255|unique:m_user,email',
                'password' => 'required|string|min:5|max:255',
                'terms' => 'required'
            ]);

            Log::info('Validation passed', ['email' => $validated['email']]);

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'mahasiswa',
                'email_verified_at' => now(), // Auto verify
            ]);

            if (!$user) {
                Log::error('User creation returned null');
                return back()->withErrors(['error' => 'Gagal membuat user'])->withInput();
            }

            Log::info('User created', ['user_id' => $user->id_user, 'email' => $user->email]);

            // Verifikasi user tersimpan di database
            $checkUser = User::find($user->id_user);
            if (!$checkUser) {
                Log::error('User not found in database after creation', ['user_id' => $user->id_user]);
                return back()->withErrors(['error' => 'User tidak tersimpan di database'])->withInput();
            }

            // Login user
            auth()->login($user);
            
            Log::info('User logged in successfully', ['user_id' => $user->id_user]);

            return redirect('/mahasiswa/dashboard')->with('success', 'Registrasi berhasil! Selamat datang.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Register error: ' . $e->getMessage(), [
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }
}
