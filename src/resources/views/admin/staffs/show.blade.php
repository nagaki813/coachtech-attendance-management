@extends('layouts.app')

@section('title', 'スタッフ別勤怠一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/staffs/show.css') }}">
@endsection

@section('content')
    <div class="staff-attendance-container">
        <h2 class="staff-attendance-title">{{ $user->name }}さんの勤怠</h2>

        <div class="month-nav">
            <a href="{{ route('admin.staff.attendances', ['user' => $user->id, 'month' => $previousMonth]) }}" class="month-nav__link">
                ←前月
            </a>

            <div class="month-nav__current">
                {{ $currentMonth->format('Y/m') }}
            </div>

            <a href="{{ route('admin.staff.attendances', ['user' => $user->id, 'month' => $nextMonth]) }}" class="month-nav__link">
                翌月→
            </a>
        </div>

        <table class="staff-attendance-table">
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
                @foreach ($attendanceDates as $attendanceDate)
                    @php
                        $date = $attendanceDate['date'];
                        $attendance = $attendanceDate['attendance'];

                        $breakMinutes = 0;
                        $workMinutes = 0;

                        if ($attendance) {
                            $breakMinutes = $attendance->breaks->sum(function ($break) {
                                if (!$break->break_start || !$break->break_end) {
                                    return 0;
                                }

                                return \Carbon\Carbon::parse($break->break_start)
                                    ->diffInMinutes(\Carbon\Carbon::parse($break->break_end));
                            });

                            if ($attendance->clock_in && $attendance->clock_out) {
                                $workMinutes = \Carbon\Carbon::parse($attendance->clock_in)
                                    ->diffInMinutes(\Carbon\Carbon::parse($attendance->clock_out))
                                    - $breakMinutes;
                            }
                        }
                    @endphp

                    <tr>
                        <td>{{ $date->format('m/d') }}</td>
                        <td>{{ $attendance && $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}</td>
                        <td>{{ $attendance && $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}</td>
                        <td>{{ $attendance ? sprintf('%d:%02d', floor($breakMinutes / 60), $breakMinutes % 60) : '' }}</td>
                        <td>{{ $attendance ? sprintf('%d:%02d', floor($workMinutes / 60), $workMinutes % 60) : '' }}</td>
                        <td>
                            @if ($attendance)
                               <a href="{{ route('admin.attendances.show', $attendance->id) }}" class="detail-link">
                                    詳細
                                </a>
                            @else
                                <span class="detail-text">詳細</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="csv-button-area">
            <form method="GET" action="{{ route('admin.staff.attendances.csv', $user->id) }}">
                <input type="hidden"
                       name="month"
                       value="{{ $currentMonth->format('Y-m') }}">

                <button type="submit" class="csv-button">
                    CSV出力
                </button>
            </form>
        </div>
    </div>
@endsection