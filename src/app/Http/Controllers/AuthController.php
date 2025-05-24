<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // cek role
        if (! Auth::user()->hasRole('User')) {
            Auth::logout();
            return back()
                ->with('error', 'Maaf, Anda tidak memiliki akses sebagai User.');
        }

        return redirect('/');
    }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:6|confirmed',
            'tanggal_lahir'  => 'required|date',
            'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
            'nomor_telepon'  => 'required|string|max:20',
        ]);

        // Buat user baru
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('User');

        // Buat profil terkait
        $user->profile()->create([
            'user_id'       => $user->id,
            'nama_lengkap'  => $validated['name'],
            'nomor_telepon' => $validated['nomor_telepon'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'jenis_kelamin' => $validated['jenis_kelamin'],
        ]);

        // Login langsung setelah registrasi
        auth()->login($user);

        return redirect('/');
    }
    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
