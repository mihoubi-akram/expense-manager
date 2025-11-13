<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate user and generate API token.
     *
     * @param LoginRequest $request The validated login request
     * @return JsonResponse User data and authentication token
     * @throws ValidationException If credentials are invalid
     *
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        // Generate new API token for the user
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Revoke the user's current access token.
     *
     * @param Request $request The authenticated request
     * @return JsonResponse Success message
     */
    public function logout(Request $request): JsonResponse
    {
        // Delete the current access token used for this request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get the authenticated user's profile data.
     *
     * @param Request $request The authenticated request
     * @return JsonResponse User profile data
     *
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'role' => $request->user()->role->value,
                'is_manager' => $request->user()->isManager(),
                'is_employee' => $request->user()->isEmployee(),
            ],
        ]);
    }

    /**
     * Register a new user account via API.
     *
     * @param RegisterRequest $request The validated registration request
     * @return JsonResponse User data and authentication token
     */
    public function apiRegister(RegisterRequest $request): JsonResponse
    {
        // Create new user with validated data
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => Role::EMPLOYEE, //default to EMPLOYEE role
        ]);

        // Generate API token for authentication
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
            ],
            'token' => $token,
        ], 201);
    }
}
