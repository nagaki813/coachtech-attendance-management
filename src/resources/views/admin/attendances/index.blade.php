@extends('layouts.app')

@section('title','勤怠一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
@endsection

@section('content')
    <h1>勤怠一覧（管理者）</h1>

    <table border="1">
        <tr>
            <th>名前</th>
            <th>日付</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>状態</th>
        </tr>

        @foreach($attendances as $attendance)
            <tr>
                <td>{{ $attendance->user->name }}</td>
                <td>{{ $attendance->work_date }}</td>
                <td>{{ $attendance->clock_in }}</td>
                <td>{{ $attendance->clock_out }}</td>
                <td>{{ $attendance->status }}</td>
            </tr>
        @endforeach
    </table>

    {{ $attendances->links() }}
@endsection