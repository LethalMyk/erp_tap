<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('pedidos', function (Blueprint $table) {
        $table->text('obs_retirada')->nullable();
        $table->string('tapeceiro')->nullable();
        $table->enum('andamento', ['retirar', 'montado', 'desmanchado', 'produzindo', 'entregar', 'concluído']);
        $table->date('data_inicio')->nullable();
        $table->text('dificuldade')->nullable();
        $table->date('previsto_para')->nullable();
        $table->date('data_termino')->nullable();
        $table->date('data_entrega')->nullable();
        $table->boolean('ARPtech')->default(false);
    });
}

public function down()
{
    Schema::table('pedidos', function (Blueprint $table) {
        $table->dropColumn('obs_retirada');
        $table->dropColumn('tapeceiro');
        $table->dropColumn('andamento');
        $table->dropColumn('data_inicio');
        $table->dropColumn('dificuldade');
        $table->dropColumn('previsto_para');
        $table->dropColumn('data_termino');
        $table->dropColumn('data_entrega');
        $table->dropColumn('ARPtech');
    });
}

};
