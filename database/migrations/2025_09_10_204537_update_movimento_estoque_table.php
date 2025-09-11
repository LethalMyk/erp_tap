<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Instância do Schema Manager para checar foreign keys
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $foreignKeys = array_map(fn($fk) => $fk->getName(), $sm->listTableForeignKeys('movimento_estoque'));

        Schema::table('movimento_estoque', function (Blueprint $table) use ($foreignKeys) {
            // Adicionar coluna 'referencia' se não existir
            if (!Schema::hasColumn('movimento_estoque', 'referencia')) {
                $table->string('referencia')->nullable()->after('vinculo');
            }

            // Ajustar enum 'tipo' para incluir 'ajuste', se o pacote doctrine/dbal estiver instalado
            if (Schema::hasColumn('movimento_estoque', 'tipo')) {
                $table->enum('tipo', ['entrada', 'saida', 'ajuste'])->default('entrada')->change();
            }

            // Garantir que a foreign key estoque_id exista
            if (!in_array('movimento_estoque_estoque_id_foreign', $foreignKeys)) {
                $table->foreign('estoque_id')
                      ->references('id')
                      ->on('estoque')
                      ->onDelete('cascade');
            }

            // Garantir que a foreign key usuario_id exista
            if (!in_array('movimento_estoque_usuario_id_foreign', $foreignKeys)) {
                $table->foreign('usuario_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('movimento_estoque', function (Blueprint $table) {
            // Remover coluna 'referencia' se existir
            if (Schema::hasColumn('movimento_estoque', 'referencia')) {
                $table->dropColumn('referencia');
            }

            // Se desejar, pode reverter 'tipo' para apenas entrada/saida (opcional)
            if (Schema::hasColumn('movimento_estoque', 'tipo')) {
                $table->enum('tipo', ['entrada', 'saida'])->default('entrada')->change();
            }
        });
    }
};
