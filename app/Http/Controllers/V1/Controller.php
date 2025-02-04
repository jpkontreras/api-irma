<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\JsonResponse;

abstract class Controller extends BaseController
{
  /**
   * Success response method.
   *
   * @param mixed $data
   * @param string $message
   * @param int $code
   */
  protected function successResponse(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
  {
    return response()->json([
      'status' => 'success',
      'message' => $message,
      'data' => $data,
    ], $code);
  }

  /**
   * Error response method.
   *
   * @param string $message
   * @param int $code
   * @param mixed $errors
   */
  protected function errorResponse(string $message = 'Error', int $code = 400, mixed $errors = null): JsonResponse
  {
    $response = [
      'status' => 'error',
      'message' => $message,
    ];

    if ($errors !== null) {
      $response['errors'] = $errors;
    }

    return response()->json($response, $code);
  }
}
