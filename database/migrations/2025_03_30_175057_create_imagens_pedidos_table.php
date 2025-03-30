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
       Schema::create('imagens_pedidos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('pedido_id')->constrained()->onDelete('cascade');
    $table->string('caminho_imagem');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagens_pedidos');
    }
};
