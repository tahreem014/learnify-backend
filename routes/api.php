<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\StudentCourseController;
use App\Http\Controllers\StudentAssignmentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::post('/course/store', [CourseController::class, 'store']);
Route::get('/courses', [CourseController::class, 'index']);
Route::delete('/course/delete/{id}', [CourseController::class, 'destroy']);
Route::post('/course/update/{id}', [CourseController::class, 'update']);


Route::post('/assignment/store', [AssignmentController::class, 'store']);
Route::get('/assignments', [AssignmentController::class, 'assignmentsForStudent']);
Route::get('/student_submissions', [StudentAssignmentController::class, 'getStudentSubmissions']);
Route::get('/courses/{id}/assignments', [AssignmentController::class, 'getCourseAssignments']);
Route::delete('/assignment/delete/{id}', [AssignmentController::class, 'destroy']);
Route::post('/assignment/update/{id}', [AssignmentController::class, 'update']);


Route::post('/student_course/store', [StudentCourseController::class, 'store']);
Route::get('/student/enrolled_courses', [StudentCourseController::class, 'enrolledCourses']);
Route::post('/student/assignment_submission', [StudentAssignmentController::class, 'submit']);