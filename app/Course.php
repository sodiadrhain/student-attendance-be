<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'course_code', 'faculty_id', 'department_id', 'faculty_course', 'department_course'
    ];

    public function faculty()
    {
        return $this->belongsTo('App\Faculty');
    }

    public function department()
    {
        return $this->belongsTo('App\Department');
    }
}
