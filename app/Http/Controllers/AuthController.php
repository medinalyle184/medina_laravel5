<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\SampleNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user and return an API token.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'message' => 'The email has already been registered.',
                'error' => 'email_already_exists',
            ], 409);
        }

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Send welcome notification
        $user->notify(new SampleNotification(
            'Welcome!',
            'Thank you for registering on our platform. Your account has been created successfully.'
        ));

        try {
            $token = $user->createToken('api-token')->plainTextToken;
        } catch (\Exception $e) {
            // handle cases where personal_access_tokens table is missing or DB error
            return response()->json([
                'message' => 'Failed to generate access token.',
                'error' => 'token_creation_failed',
                'details' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Registration successful.',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    /**
     * Register a new user and return 3 API tokens.
     */
    public function registerWithThreeTokens(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (User::where('email', $validated['email'])->exists()) {
            return response()->json([
                'message' => 'The email has already been registered.',
                'error' => 'email_already_exists',
            ], 409);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Send welcome notification
        $user->notify(new SampleNotification(
            'Welcome!',
            'Thank you for registering on our platform. Your account has been created successfully.'
        ));

        try {
            $tokens = [];
            for ($i = 1; $i <= 3; $i++) {
                $tokens[] = $user->createToken('api-token-' . $i)->plainTextToken;
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate access tokens.',
                'error' => 'token_creation_failed',
                'details' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Registration successful with 3 tokens.',
            'user' => $user,
            'tokens' => $tokens,
        ], 201);
    }


    /**
     * Authenticate an existing user and return an API token.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($validated)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        /** @var User $user */
        $user  = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    /**
     * Revoke the current user's token (logout).
     */
    public function logout(Request $request): JsonResponse
    {
        // Delete only the token used for this request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Return the authenticated user's profile.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}