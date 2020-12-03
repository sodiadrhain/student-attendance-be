<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'matric_no', 'department_id', 'faculty_id', 'level'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

}
