<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
