<?php
/**
 * Migration para completar campos em seller_profiles conforme Dicionário de Dados
 * Referência: docs/DATA_DICTIONARY.md
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            // Campos faltantes conforme dicionário de dados
            $table->string('identity_proof_path')->nullable()->after('address_proof_path')->comment('Caminho do documento de identidade');
            $table->string('phone', 20)->nullable()->after('identity_proof_path')->comment('Telefone comercial');
            $table->string('address')->nullable()->after('phone')->comment('Endereço completo');
            $table->string('city', 100)->nullable()->after('address')->comment('Cidade');
            $table->string('state', 2)->nullable()->after('city')->comment('Estado (UF)');
            $table->string('postal_code', 10)->nullable()->after('state')->comment('CEP formato: 00000-000');
            $table->string('bank_name', 100)->nullable()->after('postal_code')->comment('Nome do banco');
            $table->string('bank_account', 50)->nullable()->after('bank_name')->comment('Conta bancária');
            $table->timestamp('submitted_at')->nullable()->after('approved_at')->comment('Data de submissão dos documentos');
            
            // Corrigir enum status para incluir todos os valores do dicionário
            $table->enum('status', ['pending', 'pending_approval', 'approved', 'rejected', 'suspended'])->default('pending')->change();
            
            // Ajustar tamanhos conforme dicionário
            $table->string('document_type', 10)->nullable()->change();
            $table->string('document_number', 20)->nullable()->change();
            $table->string('mp_user_id', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'identity_proof_path',
                'phone',
                'address', 
                'city',
                'state',
                'postal_code',
                'bank_name',
                'bank_account',
                'submitted_at'
            ]);
            
            // Reverter enum status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->change();
        });
    }
};