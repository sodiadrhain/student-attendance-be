<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{
    protected $fillable = [
        'attendance_class_id', 'student_id'
    ];

    public function student()
    {
        return $this->belongsTo('App\Student');
    }
}
