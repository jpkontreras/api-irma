<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\V1\Controller;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class StaffController extends Controller
{
  /**
   * Display a listing of staff members.
   */
  public function index(): JsonResponse
  {
    $staff = Staff::with('restaurant')->get();

    return $this->successResponse($staff);
  }

  /**
   * Store a newly created staff member.
   *
   * @throws ValidationException
   */
  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'restaurant_id' => ['required', 'exists:restaurants,id'],
      'name' => ['required', 'string', 'max:255'],
      'pin' => ['required', 'string', 'min:4', 'max:6'],
      'role' => ['required', 'string', 'in:waiter,kitchen'],
      'phone' => ['nullable', 'string', 'max:20'],
      'notes' => ['nullable', 'string'],
      'preferences' => ['nullable', 'array'],
    ]);

    $validated['pin'] = Hash::make($validated['pin']);

    $staff = Staff::create($validated);

    return $this->successResponse($staff, 'Staff member created successfully', 201);
  }

  /**
   * Display the specified staff member.
   */
  public function show(Staff $staff): JsonResponse
  {
    $staff->load('restaurant');

    return $this->successResponse($staff);
  }

  /**
   * Update the specified staff member.
   *
   * @throws ValidationException
   */
  public function update(Request $request, Staff $staff): JsonResponse
  {
    $validated = $request->validate([
      'name' => ['sometimes', 'string', 'max:255'],
      'pin' => ['sometimes', 'string', 'min:4', 'max:6'],
      'role' => ['sometimes', 'string', 'in:waiter,kitchen'],
      'is_active' => ['sometimes', 'boolean'],
      'phone' => ['nullable', 'string', 'max:20'],
      'notes' => ['nullable', 'string'],
      'preferences' => ['nullable', 'array'],
    ]);

    if (isset($validated['pin'])) {
      $validated['pin'] = Hash::make($validated['pin']);
    }

    $staff->update($validated);

    return $this->successResponse($staff, 'Staff member updated successfully');
  }

  /**
   * Remove the specified staff member.
   */
  public function destroy(Staff $staff): JsonResponse
  {
    $staff->delete();

    return $this->successResponse(message: 'Staff member deleted successfully');
  }

  /**
   * Validate staff PIN.
   *
   * @throws ValidationException
   */
  public function validatePin(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'pin' => ['required', 'string'],
      'staff_id' => ['required', 'exists:staff,id'],
    ]);

    $staff = Staff::findOrFail($validated['staff_id']);

    if (!Hash::check($validated['pin'], $staff->pin)) {
      return $this->errorResponse('Invalid PIN', 401);
    }

    return $this->successResponse(message: 'PIN validated successfully');
  }
}
