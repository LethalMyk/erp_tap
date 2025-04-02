<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('terceirizadas', function (Blueprint $table) {
            $table->enum('andamento', ['em espera', 'executado', 'pronto'])->default('em espera'); // Definido corretamente como ENUM
            $table->decimal('valor', 10, 2)->nullable(); // Valor do serviço, com 2 casas decimais
 $table->enum('statusPg', ['Pendente', 'Pago', 'Parcial'])->default('Pendente')->change();        });
    }

    public function down()
    {
        Schema::table('terceirizadas', function (Blueprint $table) {
            $table->dropColumn(['andamento', 'valor', 'statusPg']);
        });
    }
};
