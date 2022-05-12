<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function createRegister()
    {
        return view('users.register');
    }

    public function storeRegister(Request $request)
    {
        $registerFields = $request->validate([
            'name' => ['required', 'min:4', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:8', 'confirmed']
        ]);

        $registerFields['password'] = bcrypt($registerFields['password']);

        $user = User::create($registerFields);

        auth()->login($user);

        return redirect('/')->with('message', 'You registered successfully!');
    }

    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'You\'re logged out!');
    }

    public function createLogin()
    {
        return view('users.login');
    }

    public function storeLogin(Request $request)
    {
        $loginFields = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (auth()->attempt($loginFields)) {
            $request->session()->regenerate();
            return redirect('/')->with('message', 'You\'re logged in!');
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }
}
