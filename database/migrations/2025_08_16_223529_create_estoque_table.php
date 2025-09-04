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
    Schema::create('estoque', function (Blueprint $table) {
        $table->id();
        $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
        $table->decimal('quantidade_disponivel', 12, 2)->default(0);
        $table->string('localizacao', 100)->nullable();
        $table->decimal('nivel_medio', 12, 2)->nullable();
        $table->decimal('quantidade_minima', 12, 2)->default(0);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('estoque');
}

};
