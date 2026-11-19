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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('model');
            $table->string('series', 10);
            $table->unsignedSmallInteger('storage_gb');
            $table->string('color');
            $table->string('condition', 20);
            $table->unsignedTinyInteger('battery_health');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('original_price')->nullable();
            $table->json('completeness');
            $table->string('warranty', 20);
            $table->string('location');
            $table->text('description');
            $table->string('status', 20)->default('pending_review');
            $table->unsignedInteger('views')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['status', 'series']);
            $table->index(['status', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
