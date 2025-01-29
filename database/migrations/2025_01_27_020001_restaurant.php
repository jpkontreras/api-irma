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

        Schema::create('restaurants', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('legal_name');
            $table->string('tax_id')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->string('website')->nullable();
            $table->text('address');
            $table->json('settings')->nullable();
            $table->enum('status', [])->default([]);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('restaurant_id')->constrained();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', [])
                ->default('');
            $table->json('settings')->nullable();
            $table->json('operation_hours')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tables', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('branch_id')->constrained();
            $table->string('number');
            $table->string('name')->nullable();
            $table->integer('capacity');
            $table->string('status')->default('available');
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('properties')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['branch_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('restaurants');
    }
};
