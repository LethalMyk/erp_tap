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
    Schema::create('produtos_comprados', function (Blueprint $table) {
        $table->id();
        $table->foreignId('despesa_id')->constrained('despesas')->onDelete('cascade');
        $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
        $table->decimal('quantidade', 10, 2);
        $table->string('unidade_medida', 50)->nullable();
        $table->decimal('valor_unitario', 10, 2)->default(0);
        $table->decimal('valor_total', 12, 2)->default(0);
        $table->text('obs')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('produtos_comprados');
}
};
