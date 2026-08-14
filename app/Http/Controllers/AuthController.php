<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Handles the single hardcoded login required by the test brief
 * (username: aldmic / password: 123abc123). Deliberately not using
 * Laravel's full auth scaffold since there is no user table/registration
 * flow in scope.
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $validUsername = config('app_auth.username', env('AUTH_USERNAME', 'aldmic'));
        $validPassword = config('app_auth.password', env('AUTH_PASSWORD', '123abc123'));

        if ($request->username === $validUsername && $request->password === $validPassword) {
            $request->session()->regenerate();
            $request->session()->put('logged_in', true);
            $request->session()->put('username', $request->username);

            return redirect()->route('movies.index');
        }

        return back()
            ->withInput($request->only('username'))
            ->with('error', __('messages.invalid_credentials'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['logged_in', 'username']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', __('messages.logged_out'));
    }
}
