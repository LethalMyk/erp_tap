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
        $table->enum('tipo', ['entrada', 'saida', 'ajuste']);
        $table->foreignId('estoque_id')->constrained('estoque')->onDelete('cascade');
        $table->decimal('quantidade', 12, 2);
        $table->string('vinculo')->nullable(); // pode guardar id de pedido/compra
        $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
        $table->timestamp('data_movimento')->useCurrent();
        $table->text('obs')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('movimento_estoque');
}

};
