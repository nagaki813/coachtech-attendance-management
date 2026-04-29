<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $correctionRequests = AttendanceCorrectionRequest::with([
            'user',
            'attendance',
        ])
        ->where('status', $status)
        ->orderBy('created_at', 'desc')
        ->paginate(10)
        ->appends(['status' => $status]);

        return view('admin.correction_requests.index', compact('correctionRequests','status'));
    }

    public function show(AttendanceCorrectionRequest $correctionRequest)
    {
        $correctionRequest->load([
            'user',
            'attendance.breaks',
            'correctionRequestBreaks',
        ]);

        return view('admin.correction_requests.show', compact('correctionRequest'));
    }

    public function approve(AttendanceCorrectionRequest $correctionRequest)
    {
        if ($correctionRequest->status !== 'pending') {
            return back()->with('error', 'すでに処理済みです');
        }

        DB::transaction(function () use ($correctionRequest) {
            $attendance = $correctionRequest->attendance;

            $attendance->update([
                'clock__in' => $correctionRequest->requested_clock_in,
                'clock_out' => $correctionRequest->requested_clock_out,
            ]);

            $attendance->breaks()->delete();

            foreach ($correctionRequest->correctionRequestBreaks as $break) {
                $attendance->breaks()->create([
                    'break_start' => $break->break_start,
                    'break_end' => $break->break_end,
                ]);
            }

            $correctionRequest->update([
                'status' => 'approved'
            ]);
        });

        return redirect()->route('admin.correction-requests.index')
            ->with('success', '承認しました');
    }

    public function reject(AttendanceCorrectionRequest $correctionRequest)
    {
        $correctionRequest->update([
            'status' => 'rejected',
        ]);

        return redirect()->route('admin.correction-requests.index', ['status' => 'pending'])
            ->with('success', '申請を却下しました');
    }
}
