@extends('layouts.app')

@section('title', '勤怠詳細')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendances/show.css') }}">
@endsection

@section('content')
    <div class="attendance-detail-container">
        <h2 class="attendance-detail-title">勤怠詳細</h2>

        <form method="POST" action="{{route('attendance_correction_requests.store', $attendance->id) }}">
            @csrf

            @php
                $isPending = $attendance->correctionRequests
                    ? $attendance->correctionRequests->where('status', 'pending')
                    ->count()
                    : 0;

                $breaks = $attendance->breaks->values();
            @endphp

            <div class="attendance-detail-card">
                <div class="detail-row">
                    <div class="detail-label">名前</div>
                    <div class="detail-value">{{ auth()->user()->name }}</div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">日付</div>
                    <div class="detail-value detail-date">
                        <span>{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年') }}</span>
                        <span>{{ \Carbon\Carbon::parse($attendance->work_date)->format('n月j日') }}</span>
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">出勤・退勤</div>
                    <div class="detail-value time-range">
                        @if ($isPending)
                            <span class="time-text">
                                {{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}
                            </span>
                        @else
                            <input type="time"
                                   name="requested_clock_in"
                                   value="{{ old('requested_clock_in', $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '') }}">
                        @endif
                        <span class="time-separator">~</span>
                        @if ($isPending)
                            <span class="time-text">
                                {{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}
                            </span>
                        @else
                            <input type="time"
                                   name="requested_clock_out"
                                   value="{{ old('requested_clock_out', $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '') }}">
                        @endif
                    </div>
                </div>

                @error('requested_clock_in')
                    <p class="error-message">{{ $message }}</p>
                @enderror

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

                    <div class="detail-row">
                        <div class="detail-label">
                            休憩{{ $i === 0 ? '' : $i + 1 }}
                        </div>

                        <div class="detail-value time-range">
                            @if ($isPending)
                                <span class="time-text">
                                    {{ $break && $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '-' }}
                                </span>
                            @else
                                <input type="time"
                                       name="breaks[{{ $i }}][break_start]"
                                       value="{{ old('breaks.' . $i . '.break_start', $break && $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '') }}">
                            @endif
                            <span class="time-separator">~</span>
                            @if ($isPending)
                                <span class="time-text">
                                    {{ $break && $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '-' }}
                                </span>
                            @else
                                <input type="time"
                                       name="breaks[{{ $i }}][break_end]"
                                       value="{{ old('breaks.' . $i . '.break_end', $break && $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '') }}">
                            @endif
                        </div>
                    </div>

                    @error("breaks.$i.break_start")
                        <p class="error-message">{{ $message }}</p>
                    @enderror

                    @error("breaks.$i.break_end")
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                @endfor

                <div class="detail-row textarea-row">
                    <div class="detail-label">備考</div>
                    <div class="detail-value">
                        @if ($isPending)
                            <p class="note-text">{{ old('note') }}</p>
                        @else
                            <textarea name="note">{{ old('note') }}</textarea>
                        @endif
                    </div>
                </div>

                @error('note')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            @if ($isPending)
                <p class="pending-message">
                    *承認待ちのため修正はできません。
                </p>
            @else
                <div class="button-area">
                    <button type="submit" class="submit-button">
                        修正
                    </button>
                </div>
            @endif
        </form>
    </div>
@endsection