@extends('layouts.app')

@section('title', 'メール認証')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
    <div class="auth-container">
        <h2 class="auth-title">メール認証</h2>

        <p class="auth-text">
            登録したメールアドレスに認証メールを送信しました。
            メール内のリンクをクリックして認証を完了してください。
        </p>

        @if (session('message'))
            <p class="success-message">{{ session('message') }}</p>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="auth-button">
                認証メールを再送信する
            </button>
        </form>
    </div>
@endsection