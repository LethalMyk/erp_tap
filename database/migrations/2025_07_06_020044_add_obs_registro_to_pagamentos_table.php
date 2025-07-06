<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up()
{
    Schema::table('pagamentos', function (Blueprint $table) {
        $table->string('obs_registro')->nullable()->after('data_registro');
    });
}

public function down()
{
    Schema::table('pagamentos', function (Blueprint $table) {
        $table->dropColumn('obs_registro');
    });
}

};
