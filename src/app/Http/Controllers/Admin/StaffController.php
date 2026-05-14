<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = User::where('role', 'user')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.staffs.index', compact('staffs'));
    }

    public function show(Request $request, User $user)
    {
        $currentMonth = $request->query('month')
            ? Carbon::parse($request->query('month'))
            : Carbon::now();

        $previousMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        $attendances = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereYear('work_date', $currentMonth->year)
            ->whereMonth('work_date', $currentMonth->month)
            ->orderBy('work_date', 'asc')
            ->get();

        return view('admin.staffs.show', compact(
            'user',
            'attendances',
            'currentMonth',
            'previousMonth',
            'nextMonth'
        ));
    }

    public function exportCsv(Request $request, User $user)
    {
        $month = $request->input('month', now()->format('Y-m'));

        $attendances = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereYear('work_date', substr($month, 0, 4))
            ->whereMonth('work_date', substr($month, 5, 2))
            ->orderBy('work_date')
            ->get();

        $fileName = $user->name . '_' . $month . '_attendance.csv';

        $headers = [
            'content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($attendances) {
            $handle = fopen('php://output', 'w');
            stream_filter_prepend(
                $handle,
                'convert.iconv.UTF-8/SJIS-win'
            );
            fputcsv($handle, [
                '日付',
                '出勤',
                '退勤',
                '休憩時間',
                '勤務時間',
            ]);

            foreach ($attendances as $attendance) {
                $breakMinutes = $attendance->breaks->sum(function ($break) {
                    if (!$break->break_start || !$break->break_end) {
                        return 0;
                    }

                    return \Carbon\Carbon::parse($break->break_start)
                        ->diffInMinutes(
                            \Carbon\Carbon::parse($break->break_end)
                        );
                });

                $workMinutes = 0;

                if ($attendance->clock_in && $attendance->clock_out) {

                    $workMinutes =
                        \Carbon\Carbon::parse($attendance->clock_in)
                            ->diffInMinutes(\Carbon\Carbon::parse($attendance->clock_out)) - $breakMinutes;
                }

                fputcsv($handle, [
                    $attendance->work_date,
                    $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-',
                    $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-',
                    floor($breakMinutes / 60) . ':' . str_pad($breakMinutes % 60, 2, '0', STR_PAD_LEFT),
                    floor($workMinutes / 60) . ':' . str_pad($workMinutes % 60, 2, '0', STR_PAD_LEFT),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
