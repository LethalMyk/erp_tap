<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Altera qntItens para permitir null e default 0
            $table->integer('qntItens')->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Reverte a alteração caso necessário
            $table->integer('qntItens')->nullable(false)->change();
        });
    }
};
