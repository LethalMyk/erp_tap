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
    Schema::create('pedidos', function (Blueprint $table) {
        $table->id('pedido_id');
        $table->foreignId('client_id');
        $table->date('data');
        $table->decimal('orcamento', 10, 2);
        $table->string('status')->default('pendente');
        $table->date('prazo');
        $table->date('data_retirada')->nullable();
        $table->text('obs')->nullable();
        $table->timestamps();
    });
}
   /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }

};
