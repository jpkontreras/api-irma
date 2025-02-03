<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('staff', function (Blueprint $table) {
      $table->id();
      $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();

      // Basic staff information
      $table->string('name');
      $table->string('pin')->comment('Hashed PIN for authentication');
      $table->string('role')->comment('waiter/kitchen');
      $table->boolean('is_active')->default(true);

      // Additional information
      $table->string('phone')->nullable();
      $table->text('notes')->nullable();
      $table->json('preferences')->nullable()->comment('Staff UI preferences and settings');

      $table->timestamps();
      $table->softDeletes();

      // Indexes
      $table->index('role');
      $table->index('is_active');
      $table->index(['restaurant_id', 'role']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('staff');
  }
};
