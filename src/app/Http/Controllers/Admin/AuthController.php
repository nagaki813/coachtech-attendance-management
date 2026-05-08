<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function login(Request $request)
    {
        if (auth()->check()) {
            return redirect()->route('admin.attendances.index');
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            session(['role' => auth()->user()->role]);

            if (auth()->user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => '管理者ではありません']);
            }

            return redirect()->route('admin.attendances.index');
        }

        return back()->withErrors(['email' => 'ログイン情報が違います']);
    }
}
