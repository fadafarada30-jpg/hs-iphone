<?php

use App\Enums\ProofStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->date('booking_date')->nullable()->after('shipping_address');
            $table->string('booking_time', 5)->nullable()->after('booking_date');
            $table->text('cod_address')->nullable()->after('booking_time');

            $table->foreignId('driver_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();

            $table->timestamp('departed_at')->nullable()->after('notes');
            $table->string('departure_photo_self')->nullable()->after('departed_at');
            $table->string('departure_photo_location')->nullable()->after('departure_photo_self');
            $table->text('departure_notes')->nullable()->after('departure_photo_location');

            $table->string('sold_proof_photo')->nullable()->after('departure_notes');
            $table->text('sold_proof_notes')->nullable()->after('sold_proof_photo');
            $table->string('sold_proof_status', 20)->default(ProofStatus::None->value)->after('sold_proof_notes');
            $table->text('sold_proof_rejected_note')->nullable()->after('sold_proof_status');
            $table->foreignId('sold_proof_reviewed_by')->nullable()->after('sold_proof_rejected_note')->constrained('users')->nullOnDelete();
            $table->timestamp('sold_proof_reviewed_at')->nullable()->after('sold_proof_reviewed_by');

            $table->index(['driver_id', 'booking_date']);
            $table->index('sold_proof_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['driver_id', 'booking_date']);
            $table->dropIndex(['sold_proof_status']);

            $table->dropConstrainedForeignId('driver_id');
            $table->dropConstrainedForeignId('sold_proof_reviewed_by');

            $table->dropColumn([
                'booking_date',
                'booking_time',
                'cod_address',
                'departed_at',
                'departure_photo_self',
                'departure_photo_location',
                'departure_notes',
                'sold_proof_photo',
                'sold_proof_notes',
                'sold_proof_status',
                'sold_proof_rejected_note',
                'sold_proof_reviewed_at',
            ]);
        });
    }
};
