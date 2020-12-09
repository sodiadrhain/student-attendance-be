<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttendanceClass extends Model
{
    protected $fillable = [
        'attendance_id', 'active', 'qr_code_data'
    ];

    public function attendance()
    {
        return $this->belongsTo('App\Attendance');
    }
}
