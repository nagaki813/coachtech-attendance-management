@extends('layouts.app')

@section('title','勤怠一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendances/index.css') }}">
@endsection

@section('content')
    <div class="admin-attendance-container">
        <h2 class="admin-attendance-title">
            {{ \Carbon\Carbon::parse($currentDate)->format('Y年m月j日') }}の勤怠
        </h2>

        <div class="date-nav">
            <a href="{{ route('admin.attendances.index', ['date' => $previousDate]) }}" class="date-nav__link">
                ←前日
            </a>

            <div class="date-nav__current">
                {{ \Carbon\Carbon::parse($currentDate)->format('Y/m/d') }}
            </div>

            <a href="{{ route('admin.attendances.index', ['date' => $nextDate]) }}" class="date-nav__link">
                翌日→
            </a>
        </div>

        <table class="admin-attendance-table">
            <thead>
                <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>

        <tbody>
            @foreach($attendances as $attendance)
                @php
                    $breakMinutes = $attendance->breaks->sum(function ($break) {
                        if (!$break->break_start || !$break->break_end) {
                            return 0;
                        }

                        return \Carbon\Carbon::parse($break->break_start)
                            ->diffInMinutes(\Carbon\Carbon::parse($break->break_end));
                    });

                    $workMinutes = 0;

                    if ($attendance->clock_in && $attendance->clock_out) {
                        $workMinutes = \Carbon\Carbon::parse($attendance->clock_in)
                            ->diffInMinutes(\Carbon\Carbon::parse($attendance->clock_out))
                            - $breakMinutes;
                    }
                @endphp

                <tr>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td>
                    <td>{{ sprintf('%d:%02d', floor($breakMinutes / 60), $breakMinutes % 60) }}</td>
                    <td>{{ sprintf('%d:%02d', floor($workMinutes / 60), $workMinutes % 60) }}</td>
                    <td>
                        <a href="{{ route('admin.attendances.show', $attendance->id) }}" class="detail-link">
                            詳細
                        </a>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection