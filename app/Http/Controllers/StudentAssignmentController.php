<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentAssignment;
use Illuminate\Support\Facades\Auth;

class StudentAssignmentController extends Controller
    {
    /**
     * Display a listing of the resource.
     */
    public function index()
        {
        //
        }

    public function submit(Request $request)
        {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'assignment_id' => 'required|exists:assignments,id',
            'course_id' => 'required|exists:courses,id',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        // Store file
        $path = $request->file('file')->store('student_assignments', 'public');

        // Save in DB
        $studentAssignment = StudentAssignment::create([
            'student_id' => $request->student_id,
            'assignment_id' => $request->assignment_id,
            'course_id' => $request->course_id,
            'file' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assignment submitted successfully',
            'data' => $studentAssignment,
        ]);
        }

    public function getStudentSubmissions(Request $request)
        {
        $courseId = $request->query('course_id');

        if (!$courseId) {
            return response()->json(['error' => 'Missing course ID'], 400);
            }

        $assignments = StudentAssignment::with([
            'student:id,username',
            'assignment:id,title',
            'course:id,name',
        ])
            ->where('course_id', $courseId)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'student_name' => $item->student->username ?? 'N/A',
                    'assignment_title' => $item->assignment->title ?? 'N/A',
                    'course_name' => $item->course->name ?? 'N/A',
                    'file' => asset('storage/' . $item->file),
                ];
                });

        return response()->json($assignments);
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
        //
        }

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
