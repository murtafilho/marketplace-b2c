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
        Schema::table('products', function (Blueprint $table) {
            // Adicionar campos faltantes que estão no model mas não no banco
            if (!Schema::hasColumn('products', 'brand')) {
                $table->string('brand', 100)->nullable()->after('published_at');
            }
            if (!Schema::hasColumn('products', 'model')) {
                $table->string('model', 100)->nullable()->after('brand');
            }
            if (!Schema::hasColumn('products', 'warranty_months')) {
                $table->integer('warranty_months')->nullable()->after('model');
            }
            if (!Schema::hasColumn('products', 'tags')) {
                $table->json('tags')->nullable()->after('warranty_months');
            }
            if (!Schema::hasColumn('products', 'attributes')) {
                $table->json('attributes')->nullable()->after('tags');
            }
            if (!Schema::hasColumn('products', 'dimensions')) {
                $table->json('dimensions')->nullable()->after('attributes');
            }
            if (!Schema::hasColumn('products', 'shipping_class')) {
                $table->string('shipping_class', 50)->nullable()->after('dimensions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'brand')) {
                $table->dropColumn('brand');
            }
            if (Schema::hasColumn('products', 'model')) {
                $table->dropColumn('model');
            }
            if (Schema::hasColumn('products', 'warranty_months')) {
                $table->dropColumn('warranty_months');
            }
            if (Schema::hasColumn('products', 'tags')) {
                $table->dropColumn('tags');
            }
            if (Schema::hasColumn('products', 'attributes')) {
                $table->dropColumn('attributes');
            }
            if (Schema::hasColumn('products', 'dimensions')) {
                $table->dropColumn('dimensions');
            }
            if (Schema::hasColumn('products', 'shipping_class')) {
                $table->dropColumn('shipping_class');
            }
        });
    }
};