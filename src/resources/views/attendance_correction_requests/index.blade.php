@extends('layouts.app')

@section('title', '申請一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_correction_requests/index.css') }}">
@endsection

@section('content')
    <div class="request-list-container">
        <h2 class="request-list-title">申請一覧</h2>

        <div class="request-tabs">
            <a href="{{ route('attendance_correction_requests.index', ['status' => 'pending']) }}" class="request-tab {{ $status === 'pending' ? 'active' : '' }}">
                承認待ち
            </a>

            <a href="{{ route('attendance_correction_requests.index', ['status' => 'approved']) }}" class="request-tab {{ $status === 'approved' ? 'active' : '' }}">
                承認済み
            </a>
        </div>

        <table class="request-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>対象日</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($correctionRequests as $request)
                    <tr>
                        <td>
                            {{ $request->status === 'pending' ? '承認待ち' : ($request->status === 'approved' ? '承認済み' : '却下') }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($request->attendance->work_date)->format('Y/m/d') }}</td>
                        <td>{{ $request->note }}</td>
                        <td>{{ $request->created_at->format('Y/m/d') }}</td>
                        <td>
                            <a href="{{ route('attendances.show', $request->attendance_id) }}" class="detail-link">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">申請はありません。</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection