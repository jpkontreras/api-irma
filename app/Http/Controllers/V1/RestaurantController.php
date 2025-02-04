<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class RestaurantController extends Controller
{
  /**
   * Display a listing of restaurants.
   */
  public function index(): JsonResponse
  {
    $restaurants = Restaurant::with(['tables', 'staff'])->get();

    return $this->successResponse($restaurants);
  }

  /**
   * Store a newly created restaurant.
   *
   * @throws ValidationException
   */
  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'email', 'max:255'],
      'phone' => ['required', 'string', 'max:20'],
      'address' => ['required', 'string'],
      'operating_hours' => ['required', 'array'],
      'timezone' => ['required', 'string'],
    ]);

    $validated['slug'] = Str::slug($validated['name']);

    $restaurant = Restaurant::create($validated);

    return $this->successResponse($restaurant, 'Restaurant created successfully', 201);
  }

  /**
   * Display the specified restaurant.
   */
  public function show(Restaurant $restaurant): JsonResponse
  {
    $restaurant->load(['tables', 'staff', 'menuItems']);

    return $this->successResponse($restaurant);
  }

  /**
   * Update the specified restaurant.
   *
   * @throws ValidationException
   */
  public function update(Request $request, Restaurant $restaurant): JsonResponse
  {
    $validated = $request->validate([
      'name' => ['sometimes', 'string', 'max:255'],
      'email' => ['sometimes', 'email', 'max:255'],
      'phone' => ['sometimes', 'string', 'max:20'],
      'address' => ['sometimes', 'string'],
      'operating_hours' => ['sometimes', 'array'],
      'timezone' => ['sometimes', 'string'],
      'is_active' => ['sometimes', 'boolean'],
    ]);

    if (isset($validated['name'])) {
      $validated['slug'] = Str::slug($validated['name']);
    }

    $restaurant->update($validated);

    return $this->successResponse($restaurant, 'Restaurant updated successfully');
  }

  /**
   * Remove the specified restaurant.
   */
  public function destroy(Restaurant $restaurant): JsonResponse
  {
    $restaurant->delete();

    return $this->successResponse(message: 'Restaurant deleted successfully');
  }
}
