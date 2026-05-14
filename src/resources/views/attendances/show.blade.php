@extends('layouts.app')

@section('title', '勤怠詳細・修正申請')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
    <h1>勤怠詳細</h1>

    <p>日付：{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y-m-d') }}</p>
    <p>出勤：{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</p>
    <p>退勤：{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</p>

    <h3>休憩</h3>
    @forelse ($attendance->breaks as $break)
        <p>
            {{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '-' }}
            ～
            {{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '-' }}
        </p>
    @empty
        <p>休憩はありません。</p>
    @endforelse

    <hr>

    <h2>修正申請</h2>

    <form method="POST" action="{{ route('attendance_correction_requests.store', $attendance->id) }}">
        @csrf

        <div>
            <label>出勤時間</label>
            <input
                type="time"
                name="requested_clock_in"
               value="{{ old('requested_clock_in', $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '') }}">
        </div>
        @error('requested_clock_in')
            <p class="error-message">{{ $message }}</p>
        @enderror

        <div>
            <label>退勤時間</label>
            <input
                type="time"
                name="requested_clock_out"
                value="{{ old('requested_clock_out', $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '') }}">
        </div>
        @error('requested_clock_out')
            <p class="error-message">{{ $message }}</p>
        @enderror

        @php
            $breaks = $attendance->breaks->values();
        @endphp

        @for ($i = 0; $i < 2; $i++)
            @php
                $break = $breaks->get($i);
            @endphp

            <div>
                <label>休憩{{ $i + 1 }}開始</label>
                <input
                    type="time"
                    name="breaks[{{ $i }}][break_start]"
                    value="{{ old("breaks.$i.break_start", $break && $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '') }}">
            </div>
            @error("breaks.$i.break_start")
                <p class="error-message">{{ $message }}</p>
            @enderror

            <div>
                <label>休憩{{ $i + 1 }}終了</label>
                <input
                    type="time"
                    name="breaks[{{ $i }}][break_end]"
                    value="{{ old("breaks.$i.break_end", $break && $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '') }}">
            </div>
            @error("breaks.$i.break_end")
                <p class="error-message">{{ $message }}</p>
            @enderror
        @endfor

        <div>
            <label>備考</label>
            <textarea name="note">{{ old('note') }}</textarea>
        </div>
        @error('note')
            <p class="error-message">{{ $message }}</p>
        @enderror

        <button type="submit">修正申請</button>
    </form>
@endsection