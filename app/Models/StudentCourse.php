<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCourse extends Model
{
     protected $fillable = [
        'student_id',
        'course_id',
    ];
}
