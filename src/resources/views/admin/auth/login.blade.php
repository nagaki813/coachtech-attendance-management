@extends('layouts.app')

@section('title', '管理者ログイン')

@section('css')
    <link rel="stylesheet" href="">
@endsection

@section('content')
    <div class="login-container">
        <h2>管理者ログイン</h2>

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf

            <div>
                <label>メールアドレス</label>
                <input type="email" name="email">
            </div>

            <div>
                <label>パスワード</label>
                <input type="password" name="password">
            </div>

            <button type="submit">ログイン</button>
        </form>
    </div>
@endsection