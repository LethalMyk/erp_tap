<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAndamentoToPedidosTable extends Migration
{
    /**
     * Execute as alterações na tabela.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('andamento')->default('RETIRAR');  // Valor padrão 'retirar'
        });
    }

    /**
     * Reverter as alterações na tabela.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('andamento');  // Remove a coluna 'andamento'
        });
    }
}
