<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $fillable = [
        'faculty_name', 'campus'
    ];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    public function department()
    {
        return $this->hasOne('App\Department');
    }
}
