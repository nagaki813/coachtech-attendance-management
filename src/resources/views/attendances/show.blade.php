@extends('layouts.app')

@section('title', '勤怠詳細・修正申請')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
    <h1>勤怠詳細</h1>

    <p>日付：{{ $attendance->work_date }}</p>
    <p>出勤：{{ $attendance->clock_in }}</p>
    <p>退勤：{{ $attendance->clock_out }}</p>

    <h3>休憩</h3>
    @foreach($attendance->breaks as $break)
        <p>
            {{ $break->break_start }} ～ {{ $break->break_end }}
        </p>
    @endforeach

    <hr>

    <h2>修正申請</h2>

    <form method="POST" action="{{ route('attendance_correction_requests.store', $attendance->id) }}">
        @csrf

        <div>
            <label>出勤時間</label>
            <input type="datetime-local" name="requested_clock_in">
        </div>

        <div>
            <label>退勤時間</label>
            <input type="datetime-local" name="requested_clock_out">
        </div>

        <div>
            <label>休憩開始</label>
            <input type="datetime-local" name="breaks[0][break_start]">
        </div>

        <div>
            <label>休憩終了</label>
            <input type="datetime-local" name="breaks[0][break_end]">
        </div>

        <div>
            <label>備考</label>
            <textarea name="note"></textarea>
        </div>

        <button type="submit">修正申請</button>
    </form>
@endsection