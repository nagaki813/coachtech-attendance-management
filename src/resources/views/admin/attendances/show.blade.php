@extends('layouts.app')

@section('title', '勤怠詳細')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendances/show.css') }}">
@endsection

@section('content')
    <div class="attendance-detail-container">
        <h2 class="attendance-detail-title">勤怠詳細</h2>

        <form action="{{ route('admin.attendances.update', $attendance->id) }}" method="POST">
            @csrf
            @method('PUT')

            <table class="attendance-detail-table">
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td>{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年m月d日') }}</td>
                </tr>

                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <div class="time-range">
                            <input type="time" name="clock_in" value="{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}">

                            <span>～</span>

                            <input type="time" name="clock_out" value="{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}">
                        </div>
                    </td>
                </tr>

                @php
                    $breaks = $attendance->breaks->values();
                @endphp

                @for ($i = 0; $i < 2; $i++)
                    @php
                        $break = $breaks->get($i);
                    @endphp

                    <tr>
                        <th>休憩{{ $i + 1 }}</th>
                        <td>
                            <div class="time-range">
                                <input type="time"
                                       name="breaks[{{ $i }}][break_start]"
                                       value="{{ $break && $break->break_start ? \carbon\Carbon::parse($break->break_start)->format('H:i') : '' }}">

                                <span>～</span>

                                <input type="time"
                                       name="breaks[{{ $i }}][break_end]"
                                       value="{{ $break && $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '' }}">
                            </div>
                        </td>
                    </tr>
                @endfor

                <tr>
                    <th>備考</th>
                    <td>
                        <textarea name="note" class="note-textarea">{{ $attendance->note }}</textarea>
                    </td>
                </tr>
            </table>

            <div class="attendance-detail-actions">
                <button type="submit" class="edit-button">
                    修正
                </button>
            </div>
        </form>
    </div>
@endsection