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
}
