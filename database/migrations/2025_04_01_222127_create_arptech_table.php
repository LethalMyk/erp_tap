<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('arptech', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('pedido_id');
        $table->unsignedBigInteger('item_id');
        $table->enum('impermeabilizacao', ['aguardando', 'executado']);
        $table->enum('higienizacao', ['aguardando', 'executado']);
        $table->decimal('custo', 10, 2);
        $table->enum('servico_pg', ['PAGO', 'PENDENTE']);
        $table->timestamps();

    });
}

public function down()
{
    Schema::dropIfExists('arptech');
}

};
