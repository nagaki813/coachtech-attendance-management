<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('attendances.index');
    }

    public function clockIn()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if ($attendance) {
            return back()->with('error', 'すでに出勤済みです');
        }

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $today,
            'clock_in' => now(),
            'status' => 'working',
        ]);

        return back()->with('success', '出勤しました');
    }

    public function clockOut()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', '出勤していません');
        }

        if ($attendance->clock_out) {
            return back()->with('error', 'すでに退勤済みです');
        }

        $attendance->update([
            'clock_out' => now(),
            'status' => 'finished',
        ]);

        return back()->with('success', '退勤しました');
    }
}
