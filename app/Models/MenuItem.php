<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class MenuItem extends Model
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
    'slug',
    'description',
    'price',
    'category',
    'is_available',
    'options',
    'preparation_time',
    'notes',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'price' => 'decimal:2',
    'is_available' => 'boolean',
    'options' => 'array',
    'preparation_time' => 'integer',
    'deleted_at' => 'datetime',
  ];

  public function restaurant(): BelongsTo
  {
    return $this->belongsTo(Restaurant::class);
  }

  public function orders(): BelongsToMany
  {
    return $this->belongsToMany(Order::class, 'order_items')
      ->withPivot(['quantity', 'unit_price', 'total_price', 'special_instructions', 'status', 'options'])
      ->withTimestamps();
  }
}
