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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->index(); // 'game' or 'account'
            $table->string('publisher')->nullable(); // Moonton, Garena, Netflix, Spotify, etc.
            $table->string('thumbnail')->nullable();
            $table->string('banner')->nullable();
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();

            // Dynamic form configuration for Top-Up Game / Account
            $table->boolean('requires_user_id')->default(true);
            $table->string('user_id_label')->default('User ID');
            $table->boolean('requires_zone_id')->default(false);
            $table->string('zone_id_label')->default('Zone ID');

            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
