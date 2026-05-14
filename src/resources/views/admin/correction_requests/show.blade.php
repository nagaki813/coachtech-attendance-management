@extends('layouts.app')

@section('title', '修正申請詳細')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/correction_requests/show.css') }}">
@endsection

@section('content')
    <div class="correction-detail-container">
        <h2 class="correction-detail-title">勤怠詳細</h2>

        <div class="correction-detail-card">
            <div class="detail-row">
                <div class="detail-label">名前</div>
                <div class="detail-value">{{ $correctionRequest->user->name }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">日付</div>
                <div class="detail-value detail-date">
                    <span>{{ \Carbon\Carbon::parse($correctionRequest->attendance->work_date)->format('Y年') }}</span>
                    <span>{{ \Carbon\Carbon::parse($correctionRequest->attendance->work_date)->format('n月j日') }}</span>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">出勤・退勤</div>
                <div class="detail-value time-range">
                    <span>{{ $correctionRequest->requested_clock_in ? \Carbon\Carbon::parse($correctionRequest->requested_clock_in)->format('H:i') : '-' }}</span>
                    <span>~</span>
                    <span>{{ $correctionRequest->requested_clock_out ? \Carbon\Carbon::parse($correctionRequest->requested_clock_out)->format('H:i') : '-' }}</span>
                </div>
            </div>

            @forelse ($correctionRequest->correctionRequestBreaks as $index => $break)
                <div class="detail-row">
                    <div class="detail-label">
                        休憩{{ $index === 0 ? '' : $index + 1 }}
                    </div>
                    <div class="detail-value time-range">
                        <span>{{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '-' }}</span>
                        <span>~</span>
                        <span>{{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '-' }}</span>
                    </div>
                </div>
            @empty
                <div class="detail-row">
                    <div class="detail-label">休憩</div>
                    <div class="detail-value time-range">
                        <span>-</span>
                        <span>~</span>
                        <span>-</span>
                    </div>
                </div>
            @endforelse

            <div class="detail-row">
                <div class="detail-label">備考</div>
                <div class="detail-value">{{ $correctionRequest->note }}</div>
            </div>
        </div>

        <div class="approval-button-area">
            @if ($correctionRequest->status === 'pending')
                <form method="POST" action="{{ route('admin.correction-requests.approve', $correctionRequest->id) }}">
                    @csrf
                    <button type="submit" class="approval-button">承認</button>
                </form>
            @else
                <button type="button" class="approval-button approved" disabled>承認済み</button>
            @endif
        </div>
    </div>
@endsection