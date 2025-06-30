<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\StudentCourse;
use Illuminate\Support\Facades\Validator;

class StudentCourseController extends Controller
    {
    /**
     * Display a listing of the resource.
     */
    public function index()
        {
        //
        }

    public function store(Request $request)
        {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
            }

        // Check if already enrolled
        $exists = StudentCourse::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Student is already enrolled in this course.'
            ], 409);
            }

        $studentCourse = StudentCourse::create([
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student enrolled successfully',
            'data' => $studentCourse
        ], 201);
        }
  
    public function enrolledCourses(Request $request)
{
    $studentId = $request->query('user_id'); // or use $request->input('user_id') for POST

    if (!$studentId) {
        return response()->json(['success' => false, 'message' => 'Student ID is required'], 400);
    }

    $student = User::find($studentId);

    if (!$student) {
        return response()->json(['success' => false, 'message' => 'Student not found'], 404);
    }

    $courses = $student->enrolledCourses()->with('teacher:id,username')->get(['courses.id', 'courses.name', 'courses.teacher_id']);


    $data = $courses->map(function ($course) {
        return [
            'course_id' => $course->id,
            'course_name' => $course->name,
            'teacher_name' => $course->teacher->username ?? 'Unknown',
        ];
    });

    return response()->json([
        'success' => true,
        'courses' => $data
    ]);
}
    public function create()
        {
        //
        }

    /**
     * Store a newly created resource in storage.
     */



    /**
     * Display the specified resource.
     */
    public function show(string $id)
        {
        //
        }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
        {
        //
        }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
        {
        //
        }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
        {
        //
        }
    }
