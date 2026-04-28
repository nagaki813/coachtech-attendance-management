@extends('layouts.app')

@section('title', '会員登録')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
    <h1>会員登録</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <label>名前</label>
            <input type="text" name="name" value="{{ old('name') }}">
        </div>

        <div>
            <label>メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">
        </div>

        <div>
            <label>パスワード</label>
            <input type="password" name="password">
        </div>

        <div>
            <label>パスワード確認</label>
            <input type="password" name="password_confirmation">
        </div>

        <button type="submit">登録する</button>
    </form>
@endsection