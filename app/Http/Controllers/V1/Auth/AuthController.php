<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\V1\Controller;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

final class AuthController extends Controller
{
  /**
   * Handle user registration.
   */
  public function register(Request $request): JsonResponse
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'password' => ['required', 'confirmed', Password::defaults()],
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    $token = $user->createToken('auth-token')->plainTextToken;

    return $this->successResponse([
      'user' => $user,
      'token' => $token,
    ], 'User registered successfully');
  }

  /**
   * Handle staff login via PIN.
   *
   * @throws ValidationException
   */
  public function login(Request $request): JsonResponse
  {
    $request->validate([
      'pin' => ['required', 'string'],
    ]);

    $staff = Staff::where('pin', Hash::make($request->pin))
      ->where('is_active', true)
      ->first();

    if (!$staff) {
      return $this->errorResponse('Invalid PIN', 401);
    }

    $token = $staff->createToken('staff-token')->plainTextToken;

    return $this->successResponse([
      'token' => $token,
      'staff' => $staff,
    ], 'Logged in successfully');
  }

  /**
   * Handle staff logout.
   */
  public function logout(Request $request): JsonResponse
  {
    $request->user()->currentAccessToken()->delete();

    return $this->successResponse(message: 'Logged out successfully');
  }
}
