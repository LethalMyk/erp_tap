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
        Schema::create('movimento_estoque', function (Blueprint $table) {
            $table->id();

            // Tipo de movimento: entrada, saída ou ajuste
            $table->enum('tipo', ['entrada', 'saida', 'ajuste'])->default('entrada');

            // FK para estoque
            $table->foreignId('estoque_id')
                  ->constrained('estoque') // referência à tabela estoque
                  ->onDelete('cascade');

            // Quantidade movimentada
            $table->decimal('quantidade', 15, 2);

            // Vinculação opcional: pedido, ordem, etc
            $table->string('vinculo')->nullable();

            // FK para usuário
            $table->foreignId('usuario_id')
                  ->constrained('users') // referência à tabela users
                  ->onDelete('cascade');

            // Data da movimentação
            $table->dateTime('data_movimento');

            // Observações opcionais
            $table->text('obs')->nullable();

            // Timestamps padrão
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimento_estoque');
    }
};
