<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>出勤登録画面</title>
</head>
<body>
<h1>勤怠打刻</h1>

@if(session('success'))
    <p style="color: green">{{ session('success') }}</p>
@endif

@if(session('error'))
    <p style="color: red">{{ session('error') }}</p>
@endif

<hr>

@if(!$attendance)
    <form method="POST" action="{{ route('attendances.clock_in') }}">
        @csrf
        <button type="submit">出勤</button>
    </form>

@elseif($attendance->status === 'working')
    <form method="POST" action="{{ route('attendances.break_start') }}">
        @csrf
        <button type="submit">休憩開始</button>
    </form>

    <form method="POST" action="{{ route('attendances.clock_out') }}">
        @csrf
        <button type="submit">退勤</button>
    </form>

@elseif($attendance->status === 'on_break')
    <form method="POST" action="{{ route('attendances.break_end') }}">
        @csrf
        <button type="submit">休憩終了</button>
    </form>
@elseif($attendance->status === 'finished')
    <p>本日の勤務は終了しています</p>
@endif
</body>
</html>
