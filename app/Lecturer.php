<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    protected $fillable = [
        'user_id', 'title', 'department_id', 'faculty_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
