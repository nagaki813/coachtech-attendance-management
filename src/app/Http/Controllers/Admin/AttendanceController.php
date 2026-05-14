<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminAttendanceRequest;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $attendances = Attendance::with('user')
        ->orderBy('work_date', 'desc')
        ->orderBy('clock_in', 'asc')
        ->paginate(10);

        return view('admin.attendances.index', compact('attendances'));
    }

    public function show(Attendance $attendance)
    {
        $attendance->load([
            'user',
            'breaks',
        ]);

        return view('admin.attendances.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        $attendance->load(['user', 'breaks']);

        return view('admin.attendances.edit', compact('attendance'));
    }

    public function update(AdminAttendanceRequest $request, Attendance $attendance)
    {
        $data = $request->validated;

        $attendance->update([
            'clock_in' => $attendance->work_date . ' ' . $request->clock_in,
            'clock_out' => $attendance->work_date . ' ' . $request->clock_out,
            'note' => $request->note,
        ]);

        $attendance->breaks()->delete();

        foreach ($request->breaks ?? [] as $break) {
            if (!empty($break['break_start']) && !empty($break['break_end'])) {
                $attendance->breaks()->create([
                    'break_start' => $attendance->work_date . ' ' . $break['break_start'],
                    'break_end' => $attendance->work_date . ' ' . $break['break_end'],
                ]);
            }
        }

        return back()->with('success', '勤怠を修正しました');
    }
}
