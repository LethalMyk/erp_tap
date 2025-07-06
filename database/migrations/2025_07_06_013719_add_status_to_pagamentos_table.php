<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::table('pagamentos', function (Blueprint $table) {
        $table->enum('status', ['EM ABERTO', 'PAGAMENTO REGISTRADO'])->default('PAGAMENTO REGISTRADO')->after('data');
    });
}

public function down()
{
    Schema::table('pagamentos', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

};
