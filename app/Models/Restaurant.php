<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Restaurant extends Model
{
  use HasFactory;
  use SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'slug',
    'email',
    'phone',
    'address',
    'operating_hours',
    'is_active',
    'timezone',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'operating_hours' => 'array',
    'is_active' => 'boolean',
    'deleted_at' => 'datetime',
  ];

  public function tables(): HasMany
  {
    return $this->hasMany(Table::class);
  }

  public function staff(): HasMany
  {
    return $this->hasMany(Staff::class);
  }

  public function menuItems(): HasMany
  {
    return $this->hasMany(MenuItem::class);
  }

  public function orders(): HasMany
  {
    return $this->hasMany(Order::class);
  }
}
