<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('order_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('order_id')->constrained()->cascadeOnDelete();
      $table->foreignId('menu_item_id')->constrained()->restrictOnDelete();

      // Item details
      $table->unsignedInteger('quantity');
      $table->decimal('unit_price', 10, 2)->comment('Price at time of order');
      $table->decimal('total_price', 10, 2);
      $table->text('special_instructions')->nullable();

      // Additional information
      $table->json('options')->nullable()->comment('Selected item options or variations');
      $table->enum('status', ['pending', 'preparing', 'ready', 'served', 'cancelled'])
        ->default('pending');

      $table->timestamps();
      $table->softDeletes();

      // Indexes
      $table->index('status');
      $table->unique(['order_id', 'menu_item_id', 'special_instructions'], 'unique_order_item');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('order_items');
  }
};
