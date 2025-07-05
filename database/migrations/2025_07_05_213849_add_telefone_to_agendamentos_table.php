<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('agendamentos', function (Blueprint $table) {
        $table->string('telefone')->nullable()->after('endereco');
    });
}

public function down()
{
    Schema::table('agendamentos', function (Blueprint $table) {
        $table->dropColumn('telefone');
    });
}

};
