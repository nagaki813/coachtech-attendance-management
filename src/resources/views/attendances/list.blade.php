@extends('layouts.app')

@section('title', '勤怠一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendances/list.css') }}">
@endsection

@section('content')
    <div class="attendance-list-container">
        <h2 class="attendance-list-title">勤怠一覧</h2>

        <div class="month-nav">
            <a href="{{ route('attendances.list', ['month' => $previousMonth]) }}" class="month-nav__link">
                ←前月
            </a>

            <div class="month-nav__current">
                {{ $currentMonth->format('Y/m') }}
            </div>

            <a href="{{ route('attendances.list', ['month' => $nextMonth]) }}" class="month-nav__link">
                翌月→
            </a>
        </div>

        <table class="attendance-list-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($attendances as $attendance)
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
                        <td>{{ \Carbon\Carbon::parse($attendance->work_date)->format('m/d') }}</td>
                        <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td>
                        <td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td>
                        <td>{{ sprintf('%d:%02d', floor($breakMinutes / 60), $breakMinutes % 60) }}</td>
                        <td>{{ sprintf('%d:%02d', floor($workMinutes / 60), $workMinutes % 60) }}</td>
                        <td>
                            <a href="{{ route('attendances.show', $attendance->id) }}" class="detail-link">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">勤怠データがありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection