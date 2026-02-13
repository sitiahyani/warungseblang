<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ])) {

            $request->session()->regenerate();

            $role = Auth::user()->role;

            if ($role === 'admin') {
                return redirect('/admin');
            } elseif ($role === 'kasir') {
                return redirect('/kasir');
            } elseif ($role === 'pelayan') {
                return redirect('/pelayan');
            }

            // Kalau role aneh
            Auth::logout();
            return redirect('/login');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah',
        ]);
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}