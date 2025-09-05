<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela de despesas (nota principal)
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nota_id')->nullable(); // para vincular a outra despesa (auto-relacionamento)
            $table->string('descricao');
            $table->decimal('valor_total', 15, 2);
            $table->date('data')->nullable();
            $table->date('data_vencimento')->nullable();
            $table->date('data_pagamento')->nullable();
            $table->enum('status', ['PENDENTE', 'PAGO'])->default('PENDENTE');
            $table->enum('categoria', ['FORNECEDOR','AGUA','LUZ','MATERIAL','PARTICULAR','OUTROS'])->nullable();
            $table->enum('forma_pagamento', ['PIX','DINHEIRO','DÉBITO','CRÉDITO','TRANSFERÊNCIA','BOLETO','A PRAZO','CHEQUE','OUTROS'])->nullable();
            $table->string('chave_pagamento')->nullable();
            $table->string('comprovante')->nullable();
            $table->text('observacao')->nullable();
            $table->string('separador')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('nota_id')->references('id')->on('despesas')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Tabela de parcelas vinculadas à despesa
        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('despesa_id');
            $table->integer('numero_parcela')->nullable();
            $table->decimal('valor_parcela', 15, 2);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->enum('status', ['PENDENTE', 'PAGO', 'ATRASADO'])->default('PENDENTE');
            $table->string('descricao')->nullable();
            $table->string('chave_pagamento')->nullable();
            $table->string('comprovante')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('despesa_id')->references('id')->on('despesas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('parcelas', function (Blueprint $table) {
            $table->dropForeign(['despesa_id']);
        });

        Schema::table('despesas', function (Blueprint $table) {
            $table->dropForeign(['nota_id']);
            $table->dropForeign(['created_by']);
        });

        Schema::dropIfExists('parcelas');
        Schema::dropIfExists('despesas');
    }
};
