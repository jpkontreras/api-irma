<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('menu_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();

      // Item information
      $table->string('name');
      $table->string('slug')->unique();
      $table->text('description')->nullable();
      $table->decimal('price', 10, 2);
      $table->string('category');
      $table->boolean('is_available')->default(true);

      // Additional information
      $table->json('options')->nullable()->comment('Additional item options or variations');
      $table->integer('preparation_time')->nullable()->comment('Estimated preparation time in minutes');
      $table->text('notes')->nullable();

      $table->timestamps();
      $table->softDeletes();

      // Indexes
      $table->index('category');
      $table->index('is_available');
      $table->index(['restaurant_id', 'category']);
      $table->index(['restaurant_id', 'is_available']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('menu_items');
  }
};
