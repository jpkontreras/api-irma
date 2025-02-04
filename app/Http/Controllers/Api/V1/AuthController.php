<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class AuthController extends Controller
{
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
