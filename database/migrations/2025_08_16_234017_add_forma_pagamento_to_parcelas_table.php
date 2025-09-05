<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parcelas', function (Blueprint $table) {
            $table->enum('forma_pagamento', ['PIX', 'DINHEIRO', 'DÉBITO', 'CRÉDITO', 'TRANSFERÊNCIA', 'BOLETO', 'A PRAZO', 'CHEQUE', 'OUTROS'])
                  ->nullable()
                  ->after('valor_parcela'); // coloque a coluna após a desejada
        });
    }

    public function down(): void
    {
        Schema::table('parcelas', function (Blueprint $table) {
            $table->dropColumn('forma_pagamento');
        });
    }
};
