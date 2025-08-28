<?php
/**
 * Arquivo: database/migrations/2025_08_28_031209_create_seller_profiles_table.php
 * Descrição: Migration para perfis de vendedores do marketplace
 * Laravel Version: 12.x
 * Criado em: 28/08/2025
 */

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
        Schema::create('seller_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('document_type')->comment('CPF ou CNPJ'); // cpf, cnpj
            $table->string('document_number')->unique();
            $table->string('company_name')->nullable();
            $table->string('address_proof_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(10.0)->comment('Taxa de comissão do vendedor');
            $table->integer('product_limit')->default(100);
            $table->string('mp_access_token', 500)->nullable()->comment('Token do Mercado Pago');
            $table->string('mp_user_id')->nullable();
            $table->boolean('mp_connected')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_profiles');
    }
};
