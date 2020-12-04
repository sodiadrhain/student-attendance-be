<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'academic_session', 'semester', 'department_id', 'faculty_id', 'lecturer_id', 'course_id'
    ];

    public function faculty()
    {
        return $this->belongsTo('App\Faculty');
    }

    public function department()
    {
        return $this->belongsTo('App\Department');
    }

    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    public function lecturer()
    {
        return $this->belongsTo('App\Lecturer');
    }
}
