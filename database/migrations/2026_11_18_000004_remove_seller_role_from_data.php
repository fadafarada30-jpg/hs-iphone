<?php

use App\Enums\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * HS Depok buys units first, so listings belong to the shop account.
     * Former seller accounts become regular buyers.
     */
    public function up(): void
    {
        $adminId = DB::table('users')->where('role', Role::Admin->value)->value('id');

        if (! $adminId) {
            return;
        }

        DB::table('listings')->whereIn('seller_id', function ($query) {
            $query->select('id')->from('users')->where('role', 'seller');
        })->update(['seller_id' => $adminId]);

        DB::table('order_items')->whereIn('seller_id', function ($query) {
            $query->select('id')->from('users')->where('role', 'seller');
        })->update(['seller_id' => $adminId]);

        DB::table('reviews')->whereIn('seller_id', function ($query) {
            $query->select('id')->from('users')->where('role', 'seller');
        })->update(['seller_id' => $adminId]);

        DB::table('chat_threads')->whereIn('seller_id', function ($query) {
            $query->select('id')->from('users')->where('role', 'seller');
        })->update(['seller_id' => $adminId]);

        DB::table('users')->where('role', 'seller')->update(['role' => Role::Buyer->value]);
    }

    public function down(): void
    {
        // Historical seller accounts cannot be distinguished after migration.
    }
};
