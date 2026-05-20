<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('work_date', today())
            ->first();

        return view('attendances.index', compact('attendance'));
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

    public function startBreak()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', '出勤していません');
        }

        if ($attendance->status !== 'working') {
            return back()->with('error', '休憩開始できません');
        }

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => now(),
        ]);

        $attendance->update([
            'status' => 'on_break',
        ]);

        return back()->with('success', '休憩開始');
    }

    public function endBreak()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', '出勤していません');
        }

        if ($attendance->status !== 'on_break') {
            return back()->with('error', '休憩中ではありません');
        }

        $break = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->latest()
            ->first();

        if (!$break) {
            return back()->with('error', '休憩データがありません');
        }

        $break->update([
            'break_end' => now(),
        ]);

        $attendance->update([
            'status' => 'working',
        ]);

        return back()->with('success', '休憩終了');
    }

    public function show(Attendance $attendance)
    {
        abort_unless($attendance->user_id === Auth::id(), 403);

        $attendance->load('breaks');

        return view('attendances.show', compact('attendance'));
    }

    public function list(Request $request)
    {
        $currentMonth = $request->query('month')
            ? Carbon::parse($request->query('month'))
            : Carbon::now();

        $previousMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        $attendances = Attendance::with('breaks')
            ->where('user_id', auth()->id())
            ->whereBetween('work_date', [
                $startOfMonth->format('Y-m-d'),
                $endOfMonth->format('Y-m-d'),
            ])
            ->orderBy('work_date', 'asc')
            ->get()
            ->keyBy(function ($attendance) {
                return Carbon::parse($attendance->work_date)->format('Y-m-d');
            });

        $attendanceDates = collect();

        for (
            $date = $startOfMonth->copy();
            $date->lte($endOfMonth);
            $date->addDay()
        ) {
            $attendanceDates->push([
                'date' => $date->copy(),
                'attendance' => $attendances->get($date->format('Y-m-d')),
            ]);
        }

        return view('attendances.list', compact(
            'attendanceDates',
            'currentMonth',
            'previousMonth',
            'nextMonth'
        ));
    }
}
