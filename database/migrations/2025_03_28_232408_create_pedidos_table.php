<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePedidosTable extends Migration
{
 public function up()
{
    Schema::create('pedidos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cliente_id');
        $table->integer('qntItens');
        $table->date('data');
        $table->decimal('valor', 10, 2); // Valor do pedido
        $table->decimal('valorResta', 10, 2)->nullable();
        $table->string('status')->nullable();
        $table->string('andamento')->nullable();
        $table->text('obs')->nullable(); // Observações do pedido
        $table->date('prazo');
        $table->string('imagem'); // Caminho do arquivo
        $table->timestamps();
    });
}


    public function down()
    {
        Schema::dropIfExists('pedidos');
    }
}
