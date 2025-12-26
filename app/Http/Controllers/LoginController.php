<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use App\Models\User;
use App\Http\Controllers\API\AuthController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PasswordResetNotification;

class LoginController extends Controller
{
    /**
     * Display login page.
     *
     * @return View
     */
    public function show()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect based on role
            switch(Auth::user()->role) {
                case 'admin':
                case 'superadmin':
                    return redirect()->intended('/dashboard');
                case 'dosen':
                    return redirect()->intended('/dosen/dashboard');
                case 'mahasiswa':
                    return redirect()->intended('/mahasiswa/dashboard');
                default:
                    return redirect()->intended('/dashboard');
        }
        
    }
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */ public function logout(Request $request)
    {
        // Just handle standard web logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
