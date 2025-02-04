<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\V1\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class OrderController extends Controller
{
  /**
   * Display a listing of orders.
   */
  public function index(): JsonResponse
  {
    $orders = Order::with(['restaurant', 'staff', 'table', 'menuItems'])->get();

    return $this->successResponse($orders);
  }

  /**
   * Store a newly created order.
   *
   * @throws ValidationException
   */
  public function store(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'restaurant_id' => ['required', 'exists:restaurants,id'],
      'staff_id' => ['required', 'exists:staff,id'],
      'table_id' => ['required', 'exists:tables,id'],
      'total_amount' => ['required', 'numeric', 'min:0'],
      'notes' => ['nullable', 'string'],
      'local_id' => ['required', 'uuid', 'unique:orders,local_id'],
      'items' => ['required', 'array', 'min:1'],
      'items.*.menu_item_id' => ['required', 'exists:menu_items,id'],
      'items.*.quantity' => ['required', 'integer', 'min:1'],
      'items.*.unit_price' => ['required', 'numeric', 'min:0'],
      'items.*.special_instructions' => ['nullable', 'string'],
      'items.*.options' => ['nullable', 'array'],
    ]);

    // Generate unique order number
    $validated['order_number'] = 'ORD-' . strtoupper(Str::random(8));

    $order = Order::create($validated);

    // Create order items
    foreach ($validated['items'] as $item) {
      $order->menuItems()->attach($item['menu_item_id'], [
        'quantity' => $item['quantity'],
        'unit_price' => $item['unit_price'],
        'total_price' => $item['quantity'] * $item['unit_price'],
        'special_instructions' => $item['special_instructions'] ?? null,
        'options' => $item['options'] ?? null,
      ]);
    }

    $order->load(['restaurant', 'staff', 'table', 'menuItems']);

    return $this->successResponse($order, 'Order created successfully', 201);
  }

  /**
   * Display the specified order.
   */
  public function show(Order $order): JsonResponse
  {
    $order->load(['restaurant', 'staff', 'table', 'menuItems']);

    return $this->successResponse($order);
  }

  /**
   * Update the specified order.
   *
   * @throws ValidationException
   */
  public function update(Request $request, Order $order): JsonResponse
  {
    if ($order->status !== 'received') {
      return $this->errorResponse('Order cannot be modified after being processed', 422);
    }

    $validated = $request->validate([
      'total_amount' => ['sometimes', 'numeric', 'min:0'],
      'notes' => ['nullable', 'string'],
      'items' => ['sometimes', 'array', 'min:1'],
      'items.*.menu_item_id' => ['required', 'exists:menu_items,id'],
      'items.*.quantity' => ['required', 'integer', 'min:1'],
      'items.*.unit_price' => ['required', 'numeric', 'min:0'],
      'items.*.special_instructions' => ['nullable', 'string'],
      'items.*.options' => ['nullable', 'array'],
    ]);

    $order->update($validated);

    if (isset($validated['items'])) {
      // Sync order items
      $order->menuItems()->detach();
      foreach ($validated['items'] as $item) {
        $order->menuItems()->attach($item['menu_item_id'], [
          'quantity' => $item['quantity'],
          'unit_price' => $item['unit_price'],
          'total_price' => $item['quantity'] * $item['unit_price'],
          'special_instructions' => $item['special_instructions'] ?? null,
          'options' => $item['options'] ?? null,
        ]);
      }
    }

    $order->load(['restaurant', 'staff', 'table', 'menuItems']);

    return $this->successResponse($order, 'Order updated successfully');
  }

  /**
   * Remove the specified order.
   */
  public function destroy(Order $order): JsonResponse
  {
    if ($order->status !== 'received') {
      return $this->errorResponse('Order cannot be deleted after being processed', 422);
    }

    $order->delete();

    return $this->successResponse(message: 'Order deleted successfully');
  }

  /**
   * Update order status.
   *
   * @throws ValidationException
   */
  public function updateStatus(Request $request, Order $order): JsonResponse
  {
    $validated = $request->validate([
      'status' => ['required', 'string', 'in:received,preparing,ready,completed,cancelled'],
    ]);

    $order->update($validated);

    if ($validated['status'] === 'completed') {
      $order->completed_at = now();
      $order->save();
    }

    return $this->successResponse($order, 'Order status updated successfully');
  }

  /**
   * Get kitchen orders view.
   */
  public function kitchen(): JsonResponse
  {
    $orders = Order::with(['restaurant', 'staff', 'table', 'menuItems'])
      ->whereIn('status', ['received', 'preparing'])
      ->orderBy('created_at')
      ->get();

    return $this->successResponse($orders);
  }

  /**
   * Get active orders.
   */
  public function active(): JsonResponse
  {
    $orders = Order::with(['restaurant', 'staff', 'table', 'menuItems'])
      ->whereIn('status', ['received', 'preparing', 'ready'])
      ->orderBy('created_at')
      ->get();

    return $this->successResponse($orders);
  }

  /**
   * Sync offline order.
   *
   * @throws ValidationException
   */
  public function sync(Request $request, Order $order): JsonResponse
  {
    $validated = $request->validate([
      'sync_data' => ['required', 'array'],
    ]);

    $order->update([
      'sync_data' => $validated['sync_data'],
      'is_synced' => true,
    ]);

    return $this->successResponse($order, 'Order synced successfully');
  }
}
