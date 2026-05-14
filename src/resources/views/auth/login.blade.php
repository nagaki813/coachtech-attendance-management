@extends('layouts.app')

@section('title,ログイン')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <div class="auth-container">
        <h1 class="auth-title">ログイン</h1>

        <form method="POST" action="{{ route('login') }}">
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
                ログインする
            </button>

            <div class="auth-link">
                <a href="{{ route('register') }}">
                    会員登録はこちら
                </a>
            </div>
        </form>
    </div>
@endsection