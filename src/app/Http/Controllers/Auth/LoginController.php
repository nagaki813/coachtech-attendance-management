<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'ログイン情報が登録されていません',
            ])->withInput();
        }

        $request->session()->regenerate();

        if (Auth::user()->role !== 'user') {
            Auth::logout();

            return back()->withErrors([
                'email' => '一般ユーザーではありません',
            ]);
        }

        return redirect('/attendance');
    }
}
