<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'string', 'min:8', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
                'phone' => 'nullable|string|max:255',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'phone' => $validated['phone'] ?? null,
            ]);

            // Fire the Registered event (sends verification email)
            try {
                event(new Registered($user));
            } catch (\Throwable $e) {
                \Log::warning('Failed to send verification email: ' . $e->getMessage());
            }

            // Load dealer relation so frontend can identify seller accounts
            $user->load('dealer');

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'User registered successfully. Please check your email to verify your account.',
                'user' => $user,
                'token' => $token,
                'email_verified' => $user->hasVerifiedEmail(),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // Let Laravel handle validation errors naturally (422)
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Registration DB error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Registration failed. Database error — please ensure all migrations have been run.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Registration failed. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            // Load dealer relation so frontend can identify seller accounts
            $user->load('dealer');

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token,
                'two_factor_enabled' => $user->two_factor_enabled,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // Let Laravel handle validation errors naturally (422)
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Login DB error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Login failed. Database error — please ensure all migrations have been run.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Login failed. Please try again later.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Load dealer relation so frontend can identify seller accounts
        $user->load('dealer');

        return response()->json([
            'user' => $user,
            'email_verified' => $user->hasVerifiedEmail(),
        ]);
    }

    /**
     * Resend email verification notification.
     */
    public function resendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification email sent.',
        ]);
    }

    /**
     * Verify email address.
     *
     * Security: The auth token is passed via a short-lived encrypted cookie
     * instead of a URL query parameter to avoid leaking it in browser history,
     * server access logs, and Referer headers.
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        $frontendUrl = config('app.frontend_url', 'https://www.autoscout24safetrade.com');

        // Validate signed URL
        if (!$request->hasValidSignature()) {
            return redirect($frontendUrl . '/email-verified?status=invalid');
        }

        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return redirect($frontendUrl . '/email-verified?status=invalid');
        }

        $alreadyVerified = $user->hasVerifiedEmail();

        if (!$alreadyVerified) {
            $user->markEmailAsVerified();
        }

        // Create an auth token so the user is logged in after verification
        $token = $user->createToken('email_verification_token')->plainTextToken;

        // Pass token and user data via short-lived encrypted cookie (5 min)
        // instead of URL query string to prevent token leakage
        $userData = json_encode([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
        ]);

        $cookieDomain = parse_url($frontendUrl, PHP_URL_HOST);

        return redirect($frontendUrl . '/email-verified?status=success')
            ->cookie('ev_token', $token, 5, '/', $cookieDomain, true, false, false, 'Lax')
            ->cookie('ev_user', $userData, 5, '/', $cookieDomain, true, false, false, 'Lax');
    }
}
