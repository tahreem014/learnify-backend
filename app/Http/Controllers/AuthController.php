<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
    {
    /**
     * Display a listing of the resource.
     */
    public function index()
        {
        //
        }

    public function register(Request $request)
        {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id', // assuming you have a roles table
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()]);
            }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ]);
        }

    public function login(Request $request)
        {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ]);
            }

        // If you're using Laravel Sanctum or Passport, generate token:
        // $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role_id' => $user->role_id,
            ],
        ]);
        }

    public function logout(Request $request)
        {
        // Revoke the current access token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
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
