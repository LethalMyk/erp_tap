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
      Schema::create('lista_compras', function (Blueprint $table) {
    $table->id();
    $table->string('material');
    $table->decimal('metragem', 8, 2);
    $table->string('fornecedor')->nullable();
    $table->enum('situacao', ['comprado','pendente','solicitado','em falta','fora de linha'])->default('pendente');
    $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->onDelete('set null');
    $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lista_compras');
    }
};
