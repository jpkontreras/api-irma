<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('tables', function (Blueprint $table) {
      $table->id();
      $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();

      // Table information
      $table->string('name');
      $table->string('location')->comment('interior/exterior/bar');
      $table->enum('status', ['available', 'occupied'])->default('available');
      $table->unsignedSmallInteger('capacity');

      // Additional information
      $table->text('notes')->nullable();

      $table->timestamps();
      $table->softDeletes();

      // Indexes
      $table->index('status');
      $table->index('location');
      $table->index(['restaurant_id', 'status']);
      $table->index(['restaurant_id', 'location']);
    });

    // Add point column using raw SQL since Laravel's Schema builder doesn't support it directly
    DB::statement('ALTER TABLE tables ADD COLUMN coordinates POINT');
  }

  public function down(): void
  {
    Schema::dropIfExists('tables');
  }
};
