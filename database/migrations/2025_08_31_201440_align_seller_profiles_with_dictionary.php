<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            // 1. Alterar campos para tamanhos corretos conforme dicionário (mantendo NULL)
            $table->string('address_proof_path', 500)->nullable()->change();
            $table->string('identity_proof_path', 500)->nullable()->change();
            $table->text('address')->nullable()->change(); // VARCHAR(255) -> TEXT
            $table->string('bank_agency', 10)->nullable()->change(); // 20 -> 10
            $table->string('bank_account', 20)->nullable()->change(); // 50 -> 20
            $table->string('mp_user_id', 50)->nullable()->change(); // 100 -> 50
            
            // 2. Atualizar status enum removendo 'pending_approval' e usando apenas 'pending'
            // Primeiro converter pending_approval para pending
        });
        
        // Converter dados existentes
        DB::statement("UPDATE seller_profiles SET status = 'pending' WHERE status = 'pending_approval'");
        
        // Alterar enum para valores do dicionário
        DB::statement("ALTER TABLE seller_profiles MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'suspended') NOT NULL DEFAULT 'pending'");
        
        // 3. Adicionar campo faltante conforme dicionário
        Schema::table('seller_profiles', function (Blueprint $table) {
            // mp_connected deve ter default 0 (false)
            $table->boolean('mp_connected')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            // Reverter alterações
            $table->string('address_proof_path', 255)->change();
            $table->string('identity_proof_path', 255)->change();
            $table->string('address', 255)->change();
            $table->string('bank_agency', 20)->change();
            $table->string('bank_account', 50)->change();
            $table->string('mp_user_id', 100)->change();
        });
        
        // Reverter enum
        DB::statement("ALTER TABLE seller_profiles MODIFY COLUMN status ENUM('pending', 'pending_approval', 'approved', 'rejected', 'suspended') NOT NULL DEFAULT 'pending'");
    }
};
