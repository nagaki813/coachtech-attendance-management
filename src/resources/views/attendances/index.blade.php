<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h1>勤怠打刻</h1>

    @if(session('success'))
        <p sytle="color: green">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color: red">{{ session('error') }}</p>
    @endif

    <form method="POST" action="/attendance/clock-in">
        @csrf
        <button type="submit">出勤</button>
    </form>

    <form method="POST" action="/attendance/clock-out">
        @csrf
        <button type="submit">退勤</button>
    </form>
</body>
</html>
