<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AtualizarEnumFormaPagamento extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->enum('forma', [
                'PIX',
                'DEBITO',
                'DINHEIRO',
                'CREDITO À VISTA',
                'CREDITO PARCELADO',
                'BOLETO',
                'CHEQUE',
                'NA ENTREGA',
                'A PRAZO',
                'OUTROS'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            // Reverte para os valores anteriores
            $table->enum('forma', [
                'PIX',
                'DEBITO',
                'DINHEIRO',
                'CREDITO À VISTA',
                'CREDITO PARCELADO',
                'BOLETO',
                'CHEQUE',
                'OUTROS'
            ])->change();
        });
    }
}
