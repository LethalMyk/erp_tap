<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            // só tornamos nullable, sem recriar o unique
            $table->string('email')->nullable()->change();
            $table->string('cpf')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('cpf')->nullable(false)->change();
        });
    }
};
