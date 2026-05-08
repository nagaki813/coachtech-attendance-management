<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceCorrectionRequest;
use App\Models\AttendanceCorrectionRequestBreak;

class AttendanceCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $correctionRequests = AttendanceCorrectionRequest::with([
            'attendance'
            ])
            ->where('user_id', auth()->id())
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('attendance_correction_requests.index', compact(
            'correctionRequests',
            'status'
            ));
    }

    public function store(Request $request, $attendanceId)
    {
        $correction = AttendanceCorrectionRequest::create([
            'attendance_id' => $attendanceId,
            'user_id' => Auth::id(),
            'status' => 'pending',
            'requested_clock_in' => $request->requested_clock_in,
            'requested_clock_out' => $request->requested_clock_out,
            'note' => $request->note,
        ]);

        if ($request->has('breaks')) {
            foreach ($request->breaks as $break) {
                AttendanceCorrectionRequestBreak::create([
                    'correction_request_id' => $correction->id,
                    'break_start' => $break['break_start'],
                    'break_end' => $break['break_end'] ?? null,
                ]);
            }
        }

        return redirect()->route('attendances.index')
            ->with('success', '修正申請を送信しました');
    }
}
