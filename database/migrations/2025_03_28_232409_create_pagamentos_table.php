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
    Schema::create('pagamentos', function (Blueprint $table) {
        $table->id('pagamento_id');
        $table->foreignId('pedido_id');
        $table->decimal('valor', 10, 2);
        $table->string('forma');
        $table->string('descricao')->nullable(); // Permite valores nulos
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
