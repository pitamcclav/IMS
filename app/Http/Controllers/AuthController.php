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
//        print_r ($request->all());
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        print_r ($credentials);

        if (Auth::guard('staff')->attempt($credentials)) {
            $request->session()->regenerate();

            $role = Auth::guard('staff')->user()->role; // Assuming role is stored in the staff model

            switch ($role) {
                case 'admin':
                    Session::flash('success', 'You are logged in successfully');
                    return redirect()->intended('/admin/dashboard');
                case 'manager':
                    Session::flash('success', 'You are logged in successfully');
                    return redirect()->intended('/manager/dashboard');
                case 'staff':
                    Session::flash('success', 'You are logged in successfully');
                    return redirect()->intended('/staff/dashboard');
                default:
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
        Auth::guard('staff')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
