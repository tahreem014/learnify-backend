<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAssignment extends Model
    {

    protected $fillable = [
        'student_id',
        'assignment_id',
        'course_id',
        'file',
    ];

    public function assignment()
        {
        return $this->belongsTo(Assignment::class);
        }

    public function student()
        {
        return $this->belongsTo(User::class, 'student_id');
        }

    public function course()
        {
        return $this->belongsTo(Course::class);
        }

    }
