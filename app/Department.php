<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'faculty_id', 'department_name'
    ];

    public function faculty()
    {
        return $this->belongsTo('App\Faculty', 'faculty_id');
    }

    public function course()
    {
        return $this->belongsTo('App\Course');
    }
}
