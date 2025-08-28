<?php
/**
 * Arquivo: database/migrations/2025_08_28_031316_create_carts_table.php
 * Descrição: Migration para carrinho de compras
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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable()->comment('Para usuários não logados');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->json('shipping_data')->nullable()->comment('Dados de entrega por vendedor');
            $table->json('coupon_data')->nullable()->comment('Cupons aplicados');
            $table->timestamp('last_activity')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('session_id');
            $table->index('last_activity');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
