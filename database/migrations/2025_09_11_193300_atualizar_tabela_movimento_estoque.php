<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('movimento_estoque', function (Blueprint $table) {
            if (!Schema::hasColumn('movimento_estoque', 'tipo')) {
                $table->string('tipo')->after('id'); // entrada ou saída
            }
            if (!Schema::hasColumn('movimento_estoque', 'estoque_id')) {
                $table->unsignedBigInteger('estoque_id')->after('tipo');
            }
            if (!Schema::hasColumn('movimento_estoque', 'quantidade')) {
                $table->decimal('quantidade', 15, 2)->after('estoque_id');
            }
            if (!Schema::hasColumn('movimento_estoque', 'vinculo')) {
                $table->string('vinculo')->nullable()->after('quantidade'); // ex: despesa_id ou outro
            }
            if (!Schema::hasColumn('movimento_estoque', 'usuario_id')) {
                $table->unsignedBigInteger('usuario_id')->nullable()->after('vinculo');
            }
            if (!Schema::hasColumn('movimento_estoque', 'data_movimento')) {
                $table->date('data_movimento')->nullable()->after('usuario_id');
            }
            if (!Schema::hasColumn('movimento_estoque', 'obs')) {
                $table->text('obs')->nullable()->after('data_movimento');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimento_estoque', function (Blueprint $table) {
            $columns = ['tipo', 'estoque_id', 'quantidade', 'vinculo', 'usuario_id', 'data_movimento', 'obs'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('movimento_estoque', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
