@extends('layouts.app')

@section('title', '会員登録')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <div class="auth-container">
        <h1 class="auth-title">会員登録</h1>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="auth-form-group">
                <label>名前</label>
                <input type="text" name="name" value="{{ old('name') }}">
                @error("name")
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

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

            <div class="auth-form-group">
                <label>パスワード確認</label>
                <input type="password" name="password_confirmation">
            </div>

            <button type="submit" class="auth-button">
                登録する
            </button>

            <div class="auth-link">
                <a href="{{ route('login') }}">
                    ログインはこちら
                </a>
            </div>
        </form>
    </div>
@endsection