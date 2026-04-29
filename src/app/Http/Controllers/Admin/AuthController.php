<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            if (auth()->user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => '管理者ではありません']);
            }

            return redirect('/admin/attendances');
        }

        return back()->withErrors(['email' => 'ログイン情報が違います']);
    }
}
