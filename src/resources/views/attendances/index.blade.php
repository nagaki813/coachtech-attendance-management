@extends('layouts.app')

@section('title', '出勤登録画面')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendances/index.css') }}">
@endsection

@section('content')
    <div class="attendance-container">
        @if(!$attendance)
            <div class="attendance-status">勤務外</div>
        @elseif($attendance->status === 'working')
            <div class="attendance-status">出勤中</div>
        @elseif($attendance->status === 'on_break')
            <div class="attendance-status">休憩中</div>
        @elseif($attendance->status === 'finished')
            <div class="attendance-status">退勤済</div>
        @endif

        <div class="attendance-date">
            {{ now()->format('Y年n月j日') }}({{ ['日', '月', '火', '水', '木', '金', '土'][now()->dayOfWeek] }})
        </div>

        <div class="attendance-time">
            {{ now()->format('H:i') }}
        </div>

        @if(session('success'))
            <p class="flash-message success">{{ session('success') }}</p>
        @endif

        @if(session('error'))
            <p class="flash-message error">{{ session('error') }}</p>
        @endif

        <div class="attendance-actions">
            @if(!$attendance)
                <form method="POST" action="{{ route('attendances.clock_in') }}">
                    @csrf
                    <button type="submit" class="attendance-button">出勤</button>
                </form>
            @elseif($attendance->status === 'working')
                <form method="POST" action="{{ route('attendances.clock_out') }}">
                    @csrf
                    <button type="submit" class="attendance-button">退勤</button>
                </form>

                <form method="POST" action="{{ route('attendances.break_start') }}">
                    @csrf
                    <button type="submit" class="attendance-button break-button">休憩入</button>
                </form>
            @elseif($attendance->status === 'on_break')
                <form method="POST" action="{{ route('attendances.break_end') }}">
                    @csrf
                    <button type="submit" class="attendance-button break-button">休憩戻</button>
                </form>
            @elseif($attendance->status === 'finished')
                <p class="finished-message">お疲れさまでした。</p>
            @endif
        </div>
    </div>
@endsection
