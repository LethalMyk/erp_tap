<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tabela de despesas (nota principal)
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->decimal('valor_total', 15, 2);
            $table->string('categoria')->nullable();
            $table->string('separador')->nullable();
            $table->string('forma_pagamento')->nullable(); // PIX, BOLETO, etc.
            $table->text('observacao')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        // Tabela de parcelas vinculadas à despesa
        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('despesa_id')->constrained('despesas')->onDelete('cascade');
            $table->integer('numero_parcela')->nullable(); // 1, 2, 3...
            $table->decimal('valor_parcela', 15, 2);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->string('status')->default('PENDENTE'); // PENDENTE, PAGO, ATRASADO
            $table->string('chave_pagamento')->nullable();
            $table->string('comprovante')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('parcelas');
        Schema::dropIfExists('despesas');
    }
};
