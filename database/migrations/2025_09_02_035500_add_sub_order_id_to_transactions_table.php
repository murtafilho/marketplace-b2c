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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('sub_order_id')->nullable()->after('order_id')->constrained('sub_orders')->onDelete('cascade');
            $table->index(['sub_order_id', 'seller_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['sub_order_id', 'seller_id']);
            $table->dropForeign(['sub_order_id']);
            $table->dropColumn('sub_order_id');
        });
    }
};
