<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('staff')->attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::guard('staff')->user();
            $token = $user->createToken('inventory-upload')->plainTextToken;
            Session::put('api_token', $token); // Store token in session

            if ($user->hasRole('admin')) {
                Session::flash('success', 'You are logged in successfully');
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->hasRole('manager')) {
                Session::flash('success', 'You are logged in successfully');
                return redirect()->intended('/manager/dashboard');
            } elseif ($user->hasRole('staff')) {
                Session::flash('success', 'You are logged in successfully');
                return redirect()->intended('/staff/dashboard');
            } elseif ($user->hasRole('store-viewer')) {
                Session::flash('success', 'You are logged in successfully');
                return redirect()->intended('/staff/dashboard'); // or any other appropriate view
            } else {
                Auth::guard('staff')->logout();
                return redirect()->route('login')->with('error', 'Unauthorized access.');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::guard('staff')->user();
        if ($user) {
            $user->tokens()->delete(); // Revoke all tokens
        }
        Auth::guard('staff')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        
        Session::forget('api_token');

        return redirect('/');
    }
}
