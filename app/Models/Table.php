<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

final class Table extends Model
{
  use HasFactory;
  use SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'restaurant_id',
    'name',
    'location',
    'status',
    'capacity',
    'coordinates',
    'notes',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'capacity' => 'integer',
    'deleted_at' => 'datetime',
  ];

  public function restaurant(): BelongsTo
  {
    return $this->belongsTo(Restaurant::class);
  }

  public function orders(): HasMany
  {
    return $this->hasMany(Order::class);
  }

  /**
   * Get the table's coordinates as a point.
   *
   * @return array{x: float, y: float}|null
   */
  public function getCoordinatesAttribute(?string $value): ?array
  {
    if ($value === null) {
      return null;
    }

    // Parse PostgreSQL point format "(x,y)"
    if (preg_match('/^\(([\d.-]+),([\d.-]+)\)$/', $value, $matches)) {
      return [
        'x' => (float) $matches[1],
        'y' => (float) $matches[2],
      ];
    }

    return null;
  }

  /**
   * Set the table's coordinates.
   *
   * @param array{x: float, y: float}|null $value
   */
  public function setCoordinatesAttribute(?array $value): void
  {
    if ($value === null) {
      $this->attributes['coordinates'] = null;
      return;
    }

    $this->attributes['coordinates'] = DB::raw(sprintf(
      'POINT(%f, %f)',
      $value['x'],
      $value['y']
    ));
  }
}
