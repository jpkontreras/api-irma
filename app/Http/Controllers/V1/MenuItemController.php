<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\V1\Controller;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class MenuItemController extends Controller
{
  /**
   * Display a listing of menu items.
   */
  public function index(): JsonResponse
  {
    $menuItems = MenuItem::with('restaurant')->get();

    return $this->successResponse($menuItems);
  }

  /**
   * Store a newly created menu item.
   *
   * @throws ValidationException
   */
  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'restaurant_id' => ['required', 'exists:restaurants,id'],
      'name' => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'price' => ['required', 'numeric', 'min:0'],
      'category' => ['required', 'string', 'max:255'],
      'options' => ['nullable', 'array'],
      'preparation_time' => ['nullable', 'integer', 'min:1'],
      'notes' => ['nullable', 'string'],
    ]);

    $validated['slug'] = Str::slug($validated['name']);

    $menuItem = MenuItem::create($validated);

    return $this->successResponse($menuItem, 'Menu item created successfully', 201);
  }

  /**
   * Display the specified menu item.
   */
  public function show(MenuItem $menuItem): JsonResponse
  {
    $menuItem->load('restaurant');

    return $this->successResponse($menuItem);
  }

  /**
   * Update the specified menu item.
   *
   * @throws ValidationException
   */
  public function update(Request $request, MenuItem $menuItem): JsonResponse
  {
    $validated = $request->validate([
      'name' => ['sometimes', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'price' => ['sometimes', 'numeric', 'min:0'],
      'category' => ['sometimes', 'string', 'max:255'],
      'is_available' => ['sometimes', 'boolean'],
      'options' => ['nullable', 'array'],
      'preparation_time' => ['nullable', 'integer', 'min:1'],
      'notes' => ['nullable', 'string'],
    ]);

    if (isset($validated['name'])) {
      $validated['slug'] = Str::slug($validated['name']);
    }

    $menuItem->update($validated);

    return $this->successResponse($menuItem, 'Menu item updated successfully');
  }

  /**
   * Remove the specified menu item.
   */
  public function destroy(MenuItem $menuItem): JsonResponse
  {
    $menuItem->delete();

    return $this->successResponse(message: 'Menu item deleted successfully');
  }

  /**
   * Toggle menu item availability.
   */
  public function toggleAvailability(MenuItem $menuItem): JsonResponse
  {
    $menuItem->update([
      'is_available' => !$menuItem->is_available,
    ]);

    return $this->successResponse(
      $menuItem,
      $menuItem->is_available ? 'Menu item is now available' : 'Menu item is now unavailable'
    );
  }
}
