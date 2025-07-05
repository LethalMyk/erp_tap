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
        Schema::create('agendamentos', function (Blueprint $table) {
    $table->id();
    $table->enum('tipo', ['entrega', 'retirada', 'assistencia', 'orcamento']);
    $table->date('data');
    $table->time('horario');
    $table->string('nome_cliente');
    $table->text('endereco');
    $table->text('itens')->nullable(); // pode ser um json ou string simples
    $table->text('observacao')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
