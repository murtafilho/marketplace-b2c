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
        Schema::table('product_images', function (Blueprint $table) {
            if (!Schema::hasColumn('product_images', 'thumbnail_path')) {
                $table->string('thumbnail_path')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('product_images', 'width')) {
                $table->integer('width')->nullable()->after('file_size');
            }
            if (!Schema::hasColumn('product_images', 'height')) {
                $table->integer('height')->nullable()->after('width');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropColumn(['thumbnail_path', 'width', 'height']);
        });
    }
};
