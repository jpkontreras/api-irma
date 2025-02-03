<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Order extends Model
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
    'staff_id',
    'table_id',
    'order_number',
    'status',
    'total_amount',
    'notes',
    'local_id',
    'is_synced',
    'sync_data',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'total_amount' => 'decimal:2',
    'is_synced' => 'boolean',
    'sync_data' => 'array',
    'completed_at' => 'datetime',
    'deleted_at' => 'datetime',
  ];

  public function restaurant(): BelongsTo
  {
    return $this->belongsTo(Restaurant::class);
  }

  public function staff(): BelongsTo
  {
    return $this->belongsTo(Staff::class);
  }

  public function table(): BelongsTo
  {
    return $this->belongsTo(Table::class);
  }

  public function menuItems(): BelongsToMany
  {
    return $this->belongsToMany(MenuItem::class, 'order_items')
      ->withPivot(['quantity', 'unit_price', 'total_price', 'special_instructions', 'status', 'options'])
      ->withTimestamps();
  }
}
