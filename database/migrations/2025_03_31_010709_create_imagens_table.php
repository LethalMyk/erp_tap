<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagensTable extends Migration
{
    public function up()
    {
        Schema::create('imagens', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('pedido_id');
            $table->string('imagem_path');  // Para armazenar o caminho da imagem
            $table->timestamps();

            // Definir chave estrangeira (relacionamento com a tabela pedidos)
            $table->foreign('pedido_id')
                  ->references('pedido_id')
                  ->on('pedidos')
                  ->onDelete('cascade');  // Se o pedido for excluído, as imagens associadas serão removidas
        });
    }

    public function down()
    {
        Schema::dropIfExists('imagens');
    }
}
