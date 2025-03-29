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
    Schema::create('usuarios', function (Blueprint $table) {
        $table->id('usuario_id');
        $table->string('nome');
        $table->string('login')->unique();
        $table->string('senha');
        $table->string('email')->unique();
        $table->string('cargo');
        $table->timestamps();
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
