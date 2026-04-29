@extends('layouts.app')

@section('title', '修正申請一覧')

@section('css')

@endsection

@section('content')
    <div class="correction-detail-container">

        <h2>勤怠詳細</h2>

        <div class="detail-box">
            <p>名前：{{ $correctionRequest->user->name }}</p>
            <p>日付：{{ $correctionRequest->attendance->work_date }}</p>

            <hr>

            <h3>修正前</h3>
            <p>出勤：{{ $correctionRequest->attendance->clock_in }}</p>
            <p>退勤：{{ $correctionRequest->attendance->clock_out }}</p>

            <h3>修正後</h3>
            <p>出勤：{{ $correctionRequest->requested_clock_in }}</p>
            <p>退勤：{{ $correctionRequest->requested_clock_out }}</p>
        </div>

        @if ($correctionRequest->status === 'pending')
            <form action="POST" action="{{ route('admin.correction-requests.approve', $correctionRequest->id) }}">
                @csrf
                <button type="submit">承認</button>
            </form>

            <form method="POST" action="{{ route('admin.correction-requests.reject', $correctionRequest->id) }}">
                @csrf
                <button type="submit">却下</button>
            </form>
        @endif
    </div>
@endsection