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
        Schema::table('agendamentos', function (Blueprint $table) {
            // adiciona a coluna pedido_id se ainda não existir
            if (!Schema::hasColumn('agendamentos', 'pedido_id')) {
                $table->unsignedBigInteger('pedido_id')->nullable()->after('id');

                // cria chave estrangeira para pedidos (ajuste o nome da tabela se for diferente)
                $table->foreign('pedido_id')->references('id')->on('pedidos')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            if (Schema::hasColumn('agendamentos', 'pedido_id')) {
                $table->dropForeign(['pedido_id']);
                $table->dropColumn('pedido_id');
            }
        });
    }
};
