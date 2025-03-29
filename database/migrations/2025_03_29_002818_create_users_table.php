<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Chave primária (id)
            $table->string('name'); // Nome do usuário
            $table->string('email')->unique(); // E-mail (único)
            $table->timestamp('email_verified_at')->nullable(); // Data de verificação do e-mail
            $table->string('password'); // Senha do usuário
            $table->rememberToken(); // Token de lembrança
            $table->timestamps(); // Timestamps de criação e atualização
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
