<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nota_id')->nullable(); // Vincula parcela à nota principal
            $table->string('descricao');
            $table->decimal('valor', 15, 2);
            $table->date('data')->nullable();
            $table->date('data_vencimento')->nullable();
            $table->date('data_pagamento')->nullable();
            $table->enum('status', ['PENDENTE', 'PAGO'])->default('PENDENTE');
            $table->enum('categoria', ['FORNECEDOR','AGUA','LUZ','MATERIAL','PARTICULAR','OUTROS']);
            $table->enum('forma_pagamento', ['PIX','DINHEIRO','DÉBITO','CRÉDITO','TRANSFERÊNCIA','BOLETO','A PRAZO','CHEQUE','OUTROS']);
            $table->string('chave_pagamento')->nullable();
            $table->string('comprovante')->nullable();
            $table->text('observacao')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('nota_id')->references('id')->on('despesas')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            $table->dropForeign(['nota_id']);
            $table->dropForeign(['created_by']);
        });

        Schema::dropIfExists('despesas');
    }
};
