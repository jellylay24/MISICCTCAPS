<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $credentials['email'] = strtolower($credentials['email']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.',
            ], 401);
        }

        $user = Auth::user();

        // Check if faculty needs email verification
        if ($user->role === 'faculty' && is_null($user->email_verified_at)) {
            Auth::logout();
            return response()->json([
                'message' => 'Please verify your email address before logging in.',
                'needs_verification' => true,
            ], 403);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'department' => $user->department,
                'student_id' => $user->student_id,
                'is_admin' => $user->isAdmin(),
            ],
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email:rfc,dns|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,faculty',
            'department' => 'nullable|string|max:255',
            'student_id' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
            'department' => $request->department,
            'student_id' => $request->student_id,
        ]);

        if ($request->role === 'admin') {
            $user->forceFill(['email_verified_at' => now()])->save();
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'message' => 'Admin registration successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => 'admin',
                    'is_admin' => true,
                ],
            ], 201);
        }

        // Faculty - send verification
        event(new Registered($user));

        return response()->json([
            'message' => 'Registration successful. Please check your email to verify your account.',
            'needs_verification' => true,
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'department' => $user->department,
            'student_id' => $user->student_id,
            'is_admin' => $user->isAdmin(),
        ]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => __($status)]);
        }

        return response()->json(['message' => __($status)], 400);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)]);
        }

        return response()->json(['message' => __($status)], 400);
    }

    public function verifyEmail($id, $hash): JsonResponse
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }

        $user->markEmailAsVerified();

        return response()->json(['message' => 'Email verified successfully! You can now log in.']);
    }
}
