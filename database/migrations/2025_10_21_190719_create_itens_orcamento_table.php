<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('itens_orcamento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orcamento_id')->constrained('orcamentos')->onDelete('cascade');
            $table->string('item');
            $table->string('tecido')->nullable();
            $table->json('espumas')->nullable();
            $table->json('enchimentos')->nullable();
            $table->json('adicionais')->nullable();
            $table->decimal('valores_base', 10, 2)->default(0);
            $table->decimal('valor_opcionais', 10, 2)->default(0);
            $table->decimal('valor_total', 10, 2)->default(0);
            $table->integer('quantidade')->default(1);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('itens_orcamento');
    }
};
