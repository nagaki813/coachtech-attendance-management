<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequestBreak;

class AttendanceCorrectionRequest extends Model
{
    protected $fillable = [
        'attendance_id',
        'user_id',
        'status',
        'requested_clock_in',
        'requested_clock_out',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function correctionRequestBreaks()
    {
        return $this->hasMany(AttendanceCorrectionRequestBreak::class, 'correction_request_id');
    }
}
