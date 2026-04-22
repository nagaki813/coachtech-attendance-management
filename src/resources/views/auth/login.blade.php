<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
</head>
<body>
    <h1>ログイン</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label>メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">
        </div>

        <div>
            <label>パスワード</label>
            <input type="password" name="password">
        </div>

        <button type="submit">ログインする</button>
    </form>
</body>
</html>