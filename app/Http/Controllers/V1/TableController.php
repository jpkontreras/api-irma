<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class TableController extends Controller
{
  /**
   * Display a listing of tables.
   */
  public function index(): JsonResponse
  {
    $tables = Table::with('restaurant')->get();

    return $this->successResponse($tables);
  }

  /**
   * Store a newly created table.
   *
   * @throws ValidationException
   */
  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'restaurant_id' => ['required', 'exists:restaurants,id'],
      'name' => ['required', 'string', 'max:255'],
      'location' => ['required', 'string', 'in:interior,exterior,bar'],
      'capacity' => ['required', 'integer', 'min:1'],
      'coordinates' => ['nullable', 'array'],
      'coordinates.x' => ['required_with:coordinates', 'numeric'],
      'coordinates.y' => ['required_with:coordinates', 'numeric'],
      'notes' => ['nullable', 'string'],
    ]);

    $table = Table::create($validated);

    return $this->successResponse($table, 'Table created successfully', 201);
  }

  /**
   * Display the specified table.
   */
  public function show(Table $table): JsonResponse
  {
    $table->load('restaurant');

    return $this->successResponse($table);
  }

  /**
   * Update the specified table.
   *
   * @throws ValidationException
   */
  public function update(Request $request, Table $table): JsonResponse
  {
    $validated = $request->validate([
      'name' => ['sometimes', 'string', 'max:255'],
      'location' => ['sometimes', 'string', 'in:interior,exterior,bar'],
      'capacity' => ['sometimes', 'integer', 'min:1'],
      'coordinates' => ['nullable', 'array'],
      'coordinates.x' => ['required_with:coordinates', 'numeric'],
      'coordinates.y' => ['required_with:coordinates', 'numeric'],
      'notes' => ['nullable', 'string'],
    ]);

    $table->update($validated);

    return $this->successResponse($table, 'Table updated successfully');
  }

  /**
   * Remove the specified table.
   */
  public function destroy(Table $table): JsonResponse
  {
    $table->delete();

    return $this->successResponse(message: 'Table deleted successfully');
  }

  /**
   * Update table status.
   *
   * @throws ValidationException
   */
  public function updateStatus(Request $request, Table $table): JsonResponse
  {
    $validated = $request->validate([
      'status' => ['required', 'string', 'in:available,occupied'],
    ]);

    $table->update($validated);

    return $this->successResponse($table, 'Table status updated successfully');
  }
}
