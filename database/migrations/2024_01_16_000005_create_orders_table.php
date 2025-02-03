<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('orders', function (Blueprint $table) {
      $table->id();
      $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
      $table->foreignId('staff_id')->constrained()->restrictOnDelete();
      $table->foreignId('table_id')->constrained()->restrictOnDelete();

      // Order information
      $table->string('order_number')->unique();
      $table->enum('status', ['received', 'preparing', 'ready', 'completed', 'cancelled'])
        ->default('received');
      $table->decimal('total_amount', 10, 2);
      $table->text('notes')->nullable();

      // Offline support
      $table->uuid('local_id')->unique()->comment('Local ID for offline operations');
      $table->boolean('is_synced')->default(false);
      $table->json('sync_data')->nullable()->comment('Data for sync resolution');

      $table->timestamp('completed_at')->nullable();
      $table->timestamps();
      $table->softDeletes();

      // Indexes
      $table->index('status');
      $table->index('is_synced');
      $table->index(['restaurant_id', 'status']);
      $table->index(['restaurant_id', 'created_at']);
      $table->index(['staff_id', 'created_at']);
      $table->index(['table_id', 'created_at']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('orders');
  }
};
