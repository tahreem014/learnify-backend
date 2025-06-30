<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
{
    $courses = Course::with('teacher:id,username')->get(['id', 'name', 'teacher_id']);

    $formatted = $courses->map(function ($course) {
        return [
            'course_id' => $course->id,
            'course_name' => $course->name,
            'teacher_name' => optional($course->teacher)->username ?? 'N/A',
        ];
    });

    return response()->json($formatted);
}

    public function store(Request $request)
    {
         $validated = $request->validate([
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $course = Course::create($validated);

        return response()->json([
            'message' => 'Course created successfully.',
            'data' => $course
        ], 201);
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'teacher_id' => 'required|exists:users,id',
    ]);

    $course = Course::find($id);

    if (!$course) {
        return response()->json(['message' => 'Course not found'], 404);
    }

    $course->update([
        'name' => $request->name,
        'teacher_id' => $request->teacher_id,
    ]);

    return response()->json([
        'message' => 'Course updated successfully',
        'course' => $course
    ]);
}

   public function destroy($id)
{
    $course = Course::find($id);

    if (!$course) {
        return response()->json(['message' => 'Course not found'], 404);
    }

    $course->delete();

    return response()->json(['message' => 'Course deleted successfully']);
}
}
