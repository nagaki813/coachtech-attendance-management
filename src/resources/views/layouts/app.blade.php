<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>
    <header class="header">
        <div class="header__inner">
            <a href="/" class="header__logo">
                COACHTECH
            </a>

            @auth
                <nav class="header__nav">
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.attendances.index') }}">勤怠一覧</a>
                        <a href="#">スタッフ一覧</a>
                        <a href="{{ route('admin.correction-requests.index') }}">申請一覧</a>
                    @else
                        <a href="{{ route('attendances.index') }}">勤怠</a>
                        <a href="#">勤怠一覧</a>
                        <a href="#">申請</a>
                    @endif

                    <form action="{{ route('logout') }}" method="POST" class="header__logout">
                        @csrf
                        <button type="submit">ログアウト</button>
                    </form>
                </nav>
            @endauth
        </div>
    </header>

    <main class="main">
        @yield('content')
    </main>
</body>
</html>