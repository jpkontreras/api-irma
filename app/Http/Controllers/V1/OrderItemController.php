<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\V1\Controller;
use App\Models\Order;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class OrderItemController extends Controller
{
  /**
   * Store a newly created order item.
   *
   * @throws ValidationException
   */
  public function store(Request $request, Order $order): JsonResponse
  {
    if ($order->status !== 'received') {
      return $this->errorResponse('Order items cannot be added after order is processed', 422);
    }

    $validated = $request->validate([
      'menu_item_id' => ['required', 'exists:menu_items,id'],
      'quantity' => ['required', 'integer', 'min:1'],
      'special_instructions' => ['nullable', 'string'],
      'options' => ['nullable', 'array'],
    ]);

    $menuItem = MenuItem::findOrFail($validated['menu_item_id']);

    $order->menuItems()->attach($menuItem->id, [
      'quantity' => $validated['quantity'],
      'unit_price' => $menuItem->price,
      'total_price' => $menuItem->price * $validated['quantity'],
      'special_instructions' => $validated['special_instructions'] ?? null,
      'options' => $validated['options'] ?? null,
    ]);

    // Update order total
    $order->update([
      'total_amount' => $order->menuItems()->sum('order_items.total_price'),
    ]);

    $order->load('menuItems');

    return $this->successResponse($order, 'Order item added successfully', 201);
  }

  /**
   * Update the specified order item.
   *
   * @throws ValidationException
   */
  public function update(Request $request, Order $order, MenuItem $item): JsonResponse
  {
    if ($order->status !== 'received') {
      return $this->errorResponse('Order items cannot be modified after order is processed', 422);
    }

    $validated = $request->validate([
      'quantity' => ['required', 'integer', 'min:1'],
      'special_instructions' => ['nullable', 'string'],
      'options' => ['nullable', 'array'],
    ]);

    $order->menuItems()->updateExistingPivot($item->id, [
      'quantity' => $validated['quantity'],
      'total_price' => $item->price * $validated['quantity'],
      'special_instructions' => $validated['special_instructions'] ?? null,
      'options' => $validated['options'] ?? null,
    ]);

    // Update order total
    $order->update([
      'total_amount' => $order->menuItems()->sum('order_items.total_price'),
    ]);

    $order->load('menuItems');

    return $this->successResponse($order, 'Order item updated successfully');
  }

  /**
   * Remove the specified order item.
   */
  public function destroy(Order $order, MenuItem $item): JsonResponse
  {
    if ($order->status !== 'received') {
      return $this->errorResponse('Order items cannot be removed after order is processed', 422);
    }

    $order->menuItems()->detach($item->id);

    // Update order total
    $order->update([
      'total_amount' => $order->menuItems()->sum('order_items.total_price'),
    ]);

    return $this->successResponse(message: 'Order item removed successfully');
  }
}
