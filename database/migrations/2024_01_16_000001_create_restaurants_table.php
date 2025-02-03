<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('restaurants', function (Blueprint $table) {
      $table->id();

      // Basic identification
      $table->string('name');
      $table->string('slug')->unique();

      // Contact information
      $table->string('email')->nullable();
      $table->string('phone')->nullable();
      $table->text('address')->nullable();

      // Operating details
      $table->json('operating_hours')->nullable();
      $table->boolean('is_active')->default(true);
      $table->string('timezone')->default('UTC');

      $table->timestamps();
      $table->softDeletes();

      // Indexes
      $table->index('is_active');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('restaurants');
  }
};
