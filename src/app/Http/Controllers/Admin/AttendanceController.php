<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
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

        return view('admin.attendances.index', compact('correctionRequests','status'));
    }
}
