@extends('layouts.app')

@section('title', '修正申請一覧')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/correction_requests/index.css') }}">
@endsection

@section('content')
<div class="admin-correction-container">
    <h2 class="admin-correction-title">申請一覧</h2>

    <div class="admin-correction-tabs">
        <a href="{{ route('admin.correction-requests.index', ['status' => 'pending']) }}" class="admin-correction-tab {{ $status === 'pending' ? 'active' : '' }}">承認待ち</a>
        <a href="{{ route('admin.correction-requests.index', ['status' => 'approved']) }}" class="admin-correction-tab {{ $status === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>

    <table class="admin-correction-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($correctionRequests as $request)
                <tr>
                    <td>{{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                    <td>{{ $request->user->name }}</td>
                    <td>{{ $request->attendance->work_date }}</td>
                    <td>{{ $request->note }}</td>
                    <td>{{ $request->created_at->format('Y/m/d H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.correction-requests.show', $request->id) }}" class="detail-link">詳細</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">修正申請はありません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination">
        {{ $correctionRequests->links() }}
    </div>
</div>
@endsection