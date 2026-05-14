@extends('layouts.app')

@section('title', '修正申請詳細')

@section('css')

@endsection

@section('content')
    <div class="correction-detail-container">

        <h2>勤怠詳細</h2>

        <div class="detail-box">
            <p>名前：{{ $correctionRequest->user->name }}</p>
            <p>日付：{{ \Carbon\Carbon::parse($correctionRequest->attendance->work_date)->format('Y年m月d日') }}</p>

            <hr>

            <h3>修正前</h3>
            <p>出勤：{{ $correctionRequest->attendance->clock_in ? \Carbon\Carbon::parse($correctionRequest->attendance->clock_in)->format('H:i') : '-' }}</p>
            <p>退勤：{{ $correctionRequest->attendance->clock_out ? \Carbon\Carbon::parse($correctionRequest->attendance->clock_out)->format('H:i') : '-' }}</p>

            @foreach ($correctionRequest->attendance->breaks as $index => $break)
                <p>
                    休憩{{ $index + 1 }}:
                    {{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '-' }}
                    ～
                    {{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '-' }}
                </p>
            @endforeach

            <h3>修正後</h3>
            <p>出勤：{{ $correctionRequest->requested_clock_in ? \Carbon\Carbon::parse($correctionRequest->requested_clock_in)->format('H:i') : '-' }}</p>
            <p>退勤：{{ $correctionRequest->requested_clock_out ? \Carbon\Carbon::parse($correctionRequest->requested_clock_out)->format('H:i') : '-' }}</p>

            @forelse ($correctionRequest->correctionRequestBreaks as $index => $break)
                <p>
                    休憩{{ $index + 1 }}:
                    {{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '-' }}
                    ～
                    {{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '-' }}
                </p>
            @empty
                <p>休憩：-</p>
            @endforelse

            <p>備考：{{ $correctionRequest->note }}</p>
        </div>

        @if ($correctionRequest->status === 'pending')
            <form method="POST" action="{{ route('admin.correction-requests.approve', $correctionRequest->id) }}">
                @csrf
                <button type="submit">承認</button>
            </form>

            <form method="POST" action="{{ route('admin.correction-requests.reject', $correctionRequest->id) }}">
                @csrf
                <button type="submit">却下</button>
            </form>
        @else
            <button type="button" disabled>承認済み</button>
        @endif
    </div>
@endsection