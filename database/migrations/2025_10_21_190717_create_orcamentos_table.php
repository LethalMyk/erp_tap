<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orcamentos', function (Blueprint $table) {
            $table->id();
            $table->string('cliente_nome');
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('Em Análise'); // Em Análise / Aprovado / Reprovado
            $table->decimal('valor_total', 10, 2)->default(0);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('orcamentos');
    }
};
