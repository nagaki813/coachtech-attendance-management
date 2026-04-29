<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
}
