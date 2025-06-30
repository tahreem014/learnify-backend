<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Assignment;
use Illuminate\Http\Request;
use App\Models\StudentAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
    {
    /**
     * Display a listing of the resource.
     */
    public function index()
        {
        //
        }

    public function assignmentsForStudent(Request $request)
        {
        $userId = $request->query('user_id'); // or $request->input('user_id');

        if (!$userId) {
            return response()->json(['error' => 'User ID is required'], 400);
            }

        $student = User::find($userId); // manually fetch user

        if (!$student) {
            return response()->json(['error' => 'User not found'], 404);
            }

        // Get all course IDs that the student is enrolled in
        $enrolledCourseIds = $student->enrolledCourses()->pluck('courses.id');

        // Fetch assignments for those courses
        $assignments = Assignment::with('course:id,name')
            ->whereIn('course_id', $enrolledCourseIds)
            ->get(['id', 'title', 'file', 'due_date', 'course_id']);

        // Format response
        $data = $assignments->map(function ($assignment) {
            return [
                'id' => $assignment->id,
                'course_name' => $assignment->course->name ?? 'Unknown',
                'course_id' => $assignment->course->id,
                'title' => $assignment->title,
                'due_date' => $assignment->due_date,
                'file_url' => asset('storage/' . $assignment->file),
            ];
            });

        return response()->json([
            'success' => true,
            'assignments' => $data,
        ]);
        }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
        {
        //
        }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
        {
        \Log::info($request->all());
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,zip',
            'due_date' => 'required|date',
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()]);
            }

        $filePath = $request->file('file')->store('assignments', 'public');

        $assignment = Assignment::create([
            'title' => $request->title,
            'file' => $filePath,
            'due_date' => $request->due_date,
            'course_id' => $request->course_id,
        ]);

        return response()->json(['assignment' => $assignment]);
        }

    public function getStudentSubmissions()
        {
        $teacherId = Auth::id(); // assuming teacher is logged in

        $assignments = StudentAssignment::with([
            'student:id,username',
            'assignment:id,title',
            'course:id,name,teacher_id',
        ])
            ->whereHas('course', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
                })
            ->get()
            ->map(function ($item) {
                return [
                    'student_name' => $item->student->name ?? 'N/A',
                    'assignment_title' => $item->assignment->title ?? 'N/A',
                    'course_name' => $item->course->name ?? 'N/A',
                    'file' => $item->file,
                ];
                });

        return response()->json($assignments);
        }

    public function getCourseAssignments($id)
{
    $assignments = Assignment::where('course_id', $id)->get(['id', 'title', 'file', 'due_date', 'course_id']);

    if ($assignments->isEmpty()) {
        return response()->json(['message' => 'No assignments found for this course.'], 404);
    }

    return response()->json([
        'message' => 'Assignments fetched successfully.',
        'data' => $assignments
    ]);
}

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
   public function update(Request $request, $id)
{
    $assignment = Assignment::find($id);

    if (!$assignment) {
        return response()->json(['message' => 'Assignment not found'], 404);
    }

    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'due_date' => 'required|date',
        'course_id' => 'required|exists:courses,id',
        'file' => 'nullable|file|mimes:pdf,docx,jpg,png|max:2048' // optional file update
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->first()], 422);
    }

    // Update fields
    $assignment->title = $request->title;
    $assignment->due_date = $request->due_date;
    $assignment->course_id = $request->course_id;

    // If a new file is uploaded
    if ($request->hasFile('file')) {

        $file = $request->file('file')->store('assignments', 'public');
        $assignment->file = $file;
    }

    $assignment->save();

    return response()->json(['message' => 'Assignment updated successfully', 'assignment' => $assignment]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $assignment = Assignment::find($id);

    if (!$assignment) {
        return response()->json(['message' => 'Assignment not found'], 404);
    }

    // Optional: delete the file from storage if needed
    Storage::delete('path/to/files/' . $assignment->file);

    $assignment->delete();

    return response()->json(['message' => 'Assignment deleted successfully']);
}
    }
