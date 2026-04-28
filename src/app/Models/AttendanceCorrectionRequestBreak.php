<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequestBreak extends Model
{
    protected $fillable = [
        'correction_request_id',
        'break_start',
        'break_end',
    ];
}
