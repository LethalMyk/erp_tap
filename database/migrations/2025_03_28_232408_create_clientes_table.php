<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('clientes', function (Blueprint $table) {
        $table->id('client_id');
        $table->string('nome');
        $table->string('telefone')->nullable();
        $table->string('endereco');
        $table->string('email')->unique();
        $table->string('cpf')->unique();
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
