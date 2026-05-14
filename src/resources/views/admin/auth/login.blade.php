@extends('layouts.app')

@section('title', '管理者ログイン')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <div class="auth-container">
        <h1 class="auth-title">管理者ログイン</h1>

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div class="auth-form-group">
                <label>メールアドレス</label>
                <input type="email" name="email" value="{{ old('email') }}">
                @error("email")
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="auth-form-group">
                <label>パスワード</label>
                <input type="password" name="password">
                @error("password")
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="auth-button">
                管理者ログインする
            </button>
        </form>
    </div>
@endsection