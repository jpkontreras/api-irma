<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('restaurant_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // regular, combo, configurable, time_based
            $table->decimal('base_price', 10, 2);
            $table->string('code')->unique()->nullable();
            $table->enum("status", ["ok"])->default("ok");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('restaurant_id')->constrained();
            $table->string('name');
            $table->enum('type', ["regular"])->default("regular"); // regular, special, seasonal, etc.
            $table->text('description')->nullable();
            $table->json('availability')->nullable(); // Time ranges, days, seasons
            $table->enum('status', ["ok"])->default("ok");
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('menu_item_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('menu_item_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('branch_id')->nullable()->constrained(); // Add this
            $table->decimal('price', 10, 2)->nullable();
            $table->enum("status", ["ok"])->default("ok");
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['menu_id', 'menu_item_id', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_menus');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('menu_items');
    }
};
