<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            // Tornar document_type nullable temporariamente
            $table->string('document_type')->nullable()->change();
            $table->string('document_number')->nullable()->change();
            
            // Corrigir enum para incluir mais opções
            $table->enum('status', ['pending', 'pending_approval', 'approved', 'rejected', 'suspended'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->string('document_type')->nullable(false)->change();
            $table->string('document_number')->nullable(false)->change();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });
    }
};