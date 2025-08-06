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
    Schema::table('despesas', function (Blueprint $table) {
        $table->string('separador')->nullable()->after('observacao'); // ou outra posição que preferir
    });
}

public function down()
{
    Schema::table('despesas', function (Blueprint $table) {
        $table->dropColumn('separador');
    });
}

};
