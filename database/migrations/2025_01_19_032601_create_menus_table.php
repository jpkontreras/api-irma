<?php

/**
 * Menu Migration
 * 
 * This migration creates the 'menus' table which represents different menu configurations
 * for a restaurant (e.g., breakfast menu, lunch menu, dinner menu, seasonal menus).
 * 
 * Columns:
 * - id (uuid): Primary key using UUID for better scalability and distribution
 * - name (string): Name of the menu (e.g., "Lunch Menu", "Holiday Special")
 * - description (text): Optional detailed description of the menu
 * - start_time (time): Optional time when the menu becomes available (e.g., 11:00 for lunch menu)
 * - end_time (time): Optional time when the menu stops being available
 * - is_active (boolean): Flag indicating if the menu is currently active
 * - is_default (boolean): Flag indicating if this is the default menu when no other applies
 * - restaurant_id (uuid): Foreign key to the restaurants table
 * - created_at (timestamp): When the record was created
 * - updated_at (timestamp): When the record was last updated
 * 
 * Indexes:
 * - Primary Key on id
 * - Combined unique index on (restaurant_id, is_default) to ensure one default menu per restaurant
 * - Combined index on (restaurant_id, is_active) for efficient active menu queries
 * - Combined index on (start_time, end_time) for time-based queries
 * 
 * Foreign Keys:
 * - restaurant_id references id on restaurants table (with cascade delete)
 * 
 * @package IRMA
 * @subpackage Database\Migrations
 * @category Menu Management
 * @author Generated by IRMA System
 * @since 1.0.0
 */

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
        Schema::create('menus', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->foreignUlid('restaurant_id')->constrained('restaurants')->onDelete('cascade');
            $table->timestamps();

            // Ensure only one default menu per restaurant
            $table->unique(['restaurant_id', 'name'], 'unique_menu_per_restaurant');

            // Index for common queries
            $table->index(['restaurant_id', 'is_active']);
            $table->index(['start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
