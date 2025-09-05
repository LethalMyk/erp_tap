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
    Schema::table('pagamentos', function (Blueprint $table) {
        $table->enum('forma', ['PIX', 'DEBITO', 'DINHEIRO', 'CREDITO À VISTA', 'CREDITO PARCELADO', 'BOLETO', 'CHEQUE', 'OUTROS', 'A PRAZO', 'NA ENTREGA'])->change();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            //
        });
    }
};
