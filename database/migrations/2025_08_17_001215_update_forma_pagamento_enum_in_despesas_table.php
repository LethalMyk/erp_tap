<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alterar o enum para incluir 'À VISTA'
        DB::statement("
            ALTER TABLE despesas 
            MODIFY forma_pagamento ENUM(
                'À VISTA', 'PIX', 'DINHEIRO', 'DÉBITO', 'CRÉDITO', 'TRANSFERÊNCIA', 'BOLETO', 'A PRAZO', 'CHEQUE', 'OUTROS'
            ) NULL
        ");
    }

    public function down(): void
    {
        // Reverter para o enum antigo (sem 'À VISTA')
        DB::statement("
            ALTER TABLE despesas 
            MODIFY forma_pagamento ENUM(
                'PIX', 'DINHEIRO', 'DÉBITO', 'CRÉDITO', 'TRANSFERÊNCIA', 'BOLETO', 'A PRAZO', 'CHEQUE', 'OUTROS'
            ) NULL
        ");
    }
};
