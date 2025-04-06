<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('terceirizadas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipoServico', ['Impermeabilizar', 'Higienizar', 'Pintar', 'Invernizar', 'Outros']);
            $table->text('obs')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('pedido_id');
            $table->enum('andamento', ['em espera', 'executado', 'pronto'])->default('em espera');
            $table->decimal('valor', 10, 2)->nullable();
            $table->enum('statusPg', ['Pendente', 'Pago', 'Parcial'])->default('Pendente');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('terceirizadas');
    }
};
