<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AttendanceCorrectionRequest;
use App\Models\AttendanceCorrectionRequestBreak;
use App\Http\Requests\AttendanceCorrectionRequest as AttendanceCorrectionFormRequest;
use App\Models\Attendance;

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

    public function store(AttendanceCorrectionFormRequest  $request, Attendance $attendance)
    {
        $data = $request->validated();

        $correction = AttendanceCorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => Auth::id(),
            'status' => 'pending',
            'requested_clock_in' => $attendance->work_date . ' ' . $data['requested_clock_in'],
            'requested_clock_out' => $attendance->work_date . ' ' . $data['requested_clock_out'],
            'note' => $data['note'],
        ]);
        foreach ($data['breaks'] ?? [] as $break) {
            if (!empty($break['break_start']) && !empty($break['break_end'])) {
                AttendanceCorrectionRequestBreak::create([
                    'correction_request_id' => $correction->id,
                    'break_start' => $attendance->work_date . ' ' . $break['break_start'],
                    'break_end' => $attendance->work_date . ' ' .  $break['break_end'],
                ]);
            }
        }

        return redirect()->route('attendances.index')
            ->with('success', '修正申請を送信しました');
    }
}
