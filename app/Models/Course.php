<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'name',
        'teacher_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }


    public function students()
    {
        return $this->belongsToMany(User::class, 'course_students', 'course_id', 'student_id');
    }

    // Course.php
public function assignments()
{
    return $this->hasMany(Assignment::class);
}

}
