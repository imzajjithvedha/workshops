<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['bail', 'required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::validate($credentials)) {
            return back()->withInput()->withErrors([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        if (! $user->isActive()) {
            return back()->withInput()->withErrors([
                'email' => 'Your account is inactive. Please contact an administrator.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route($user->role->dashboardRoute());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
