<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (auth()->check()) {

            if (auth()->user()->role === 'admin') {
                return redirect()->route('admin.attendances.index');
            }

            return redirect()->route('attendances.index');
        }

        return view('admin.auth.login');
    }

    public function login(AdminLoginRequest $request)
    {
        if (auth()->check()) {
            return redirect()->route('admin.attendances.index');
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            session(['role' => auth()->user()->role]);

            if (auth()->user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => '管理者ではありません'])
                    ->withInput();
            }

            return redirect()->route('admin.attendances.index');
        }

        return back()->withErrors(['email' => 'ログイン情報が違います'])
            ->withInput();
    }
}
