<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;
use App\Services\OtpService;
use App\Notifications\OtpNotification;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        // Generate registration OTP and notify user (API flow)
        try {
            /** @var OtpService $otpService */
            $otpService = app(OtpService::class);
            $otp = $otpService->generate($user, 'registration');
            $user->notify(new OtpNotification($otp->code, 'registration'));
        } catch (Throwable $e) {
            // If OTP generation/notification fails, log but continue
            logger()->error('Failed to generate/send OTP: ' . $e->getMessage());
        }

        // For API registration we don't immediately issue a session token — user must verify OTP first.
        return response()->json([
            'message' => 'OTP sent',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email', // Must be a valid email
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        // Check if username is email or username
        $field = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::attempt([$field => $request->email, 'password' => $request->password])) {
            return response()->json([
                'error' => [
                    'code' => 'INVALID_CREDENTIALS',
                    'message' => 'Invalid credentials'
                ]
            ], 401);
        }

        $user = Auth::user();

        // Check if invited user needs to change password
        if ($user->status === 'invited' && !$user->password_changed_at) {
            return response()->json([
                'user' => $user,
                'token' => $user->createToken('mobile-app')->plainTextToken,
                'requires_password_change' => true
            ]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete(); // Revoke all tokens
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $user = $request->user();
        $user->update($request->only(['name', 'phone']));

        return response()->json($user);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'error' => [
                    'code' => 'INVALID_PASSWORD',
                    'message' => 'Current password is incorrect'
                ]
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Password updated successfully'
        ]);
    }

    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $email = $request->input('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            // Generic response to avoid revealing account existence
            return response()->json([
                'message' => 'If that email exists in our system, you will receive an OTP'
            ], 200);
        }

        try {
            $otpService = app(\App\Services\OtpService::class);
            $otp = $otpService->generate($user, 'password_reset');
            $user->notify(new \App\Notifications\OtpNotification($otp->code, 'password_reset'));

            return response()->json([
                'message' => 'OTP sent to your email'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => [
                    'code' => 'OTP_SEND_FAILED',
                    'message' => 'Failed to send OTP'
                ]
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'code' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'error' => [
                    'code' => 'USER_NOT_FOUND',
                    'message' => 'User not found'
                ]
            ], 404);
        }

        try {
            $otpService = app(\App\Services\OtpService::class);
            
            // Validate OTP code for password_reset context
            if (!$otpService->validate($user, 'password_reset', $request->code)) {
                return response()->json([
                    'error' => [
                        'code' => 'INVALID_OTP',
                        'message' => 'Invalid or expired OTP'
                    ]
                ], 401);
            }

            // OTP validated, update password
            $user->update([
                'password' => Hash::make($request->password),
                'password_changed_at' => now(),
                'status' => 'active',
            ]);

            return response()->json([
                'message' => 'Password reset successfully'
            ], 200);
        } catch (\Throwable $e) {
            logger()->error('Password reset failed: ' . $e->getMessage());
            return response()->json([
                'error' => [
                    'code' => 'RESET_FAILED',
                    'message' => 'Failed to reset password'
                ]
            ], 500);
        }
    }

    public function forcePasswordChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $validator->errors()
                ]
            ], 422);
        }

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'User not authenticated'
                ]
            ], 401);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Password changed successfully',
            'user' => $user,
        ]);
    }
}
