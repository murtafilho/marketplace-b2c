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
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_profiles', 'bank_name')) {
                $table->string('bank_name', 100)->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('seller_profiles', 'bank_agency')) {
                $table->string('bank_agency', 20)->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('seller_profiles', 'bank_account')) {
                $table->string('bank_account', 50)->nullable()->after('bank_agency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('seller_profiles', 'bank_name')) {
                $table->dropColumn('bank_name');
            }
            if (Schema::hasColumn('seller_profiles', 'bank_agency')) {
                $table->dropColumn('bank_agency');
            }
            if (Schema::hasColumn('seller_profiles', 'bank_account')) {
                $table->dropColumn('bank_account');
            }
        });
    }
};