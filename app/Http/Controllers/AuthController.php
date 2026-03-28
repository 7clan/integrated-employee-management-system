<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        $user = User::create($validated); // password cast = hashed in User model
        Auth::login($user);

        return redirect()->route('employee.index');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required','string','max:255'],
            'password' => ['required','string'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors(['name' => 'Invalid credentials.'])->onlyInput('name');
        }

        $request->session()->regenerate();
        return redirect()->intended(route('employee.index'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('show.login');
    }
}
