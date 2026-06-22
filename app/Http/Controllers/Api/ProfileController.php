<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(): JsonResponse
    {
        $user = request()->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'department' => $user->department,
            'student_id' => $user->student_id,
            'is_admin' => $user->isAdmin(),
            'email_verified' => !is_null($user->email_verified_at),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'department' => 'nullable|string|max:255',
            'student_id' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh(),
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Password updated successfully']);
    }
}
