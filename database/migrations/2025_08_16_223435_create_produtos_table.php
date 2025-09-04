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
    Schema::create('produtos', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('unidade_medida', 50)->nullable();
        $table->string('categoria', 100)->nullable();
        $table->text('descricao')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('produtos');
}

};
