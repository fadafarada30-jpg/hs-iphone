<?php

use App\Enums\SellRequestStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sell_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('model');
            $table->string('series', 10)->nullable();
            $table->unsignedSmallInteger('storage_gb');
            $table->string('color');
            $table->string('condition', 20);
            $table->unsignedTinyInteger('battery_health')->nullable();
            $table->unsignedBigInteger('price');
            $table->text('description')->nullable();
            $table->string('city');
            $table->json('photos')->nullable();
            $table->string('status', 20)->default(SellRequestStatus::Pending->value);
            $table->text('rejection_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('listing_id')->nullable()->constrained('listings')->nullOnDelete();
            $table->foreignId('chat_thread_id')->nullable()->constrained('chat_threads')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sell_requests');
    }
};
