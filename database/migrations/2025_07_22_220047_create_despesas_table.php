<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->enum('status', ['PENDENTE', 'PAGO', 'ATRASADO'])->default('PENDENTE');
            $table->enum('categoria', ['FORNECEDOR', 'AGUA', 'LUZ', 'MATERIAL', 'PARTICULAR', 'OUTROS']);
            $table->enum('forma_pagamento', ['PIX', 'DINHEIRO', 'BOLETO', 'CARTAO', 'TRANSFERENCIA', 'OUTROS']);
            $table->string('chave_pagamento')->nullable();
            $table->string('comprovante')->nullable(); // caminho do arquivo
            $table->text('observacao')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('despesas');
    }
};
