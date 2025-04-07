<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_servico')->unique();
            $table->foreignId('profissional_id')->nullable()    ->constrained('profissional')->onDelete('cascade');
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->onDelete('cascade');
            $table->date('data_inicio')->nullable();
            $table->date('data_termino')->nullable();
            $table->string('dificuldade')->nullable();
            $table->date('data_previsao')->nullable();
            $table->text('obs')->nullable()->nullable();
            

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};

