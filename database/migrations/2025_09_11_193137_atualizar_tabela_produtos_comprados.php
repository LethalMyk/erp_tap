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
        Schema::table('produtos_comprados', function (Blueprint $table) {
            if (!Schema::hasColumn('produtos_comprados', 'despesa_id')) {
                $table->unsignedBigInteger('despesa_id')->after('id');
            }
            if (!Schema::hasColumn('produtos_comprados', 'produto_id')) {
                $table->unsignedBigInteger('produto_id')->after('despesa_id');
            }
            if (!Schema::hasColumn('produtos_comprados', 'quantidade')) {
                $table->decimal('quantidade', 15, 2)->after('produto_id');
            }
            if (!Schema::hasColumn('produtos_comprados', 'unidade_medida')) {
                $table->string('unidade_medida')->nullable()->after('quantidade');
            }
            if (!Schema::hasColumn('produtos_comprados', 'valor_unitario')) {
                $table->decimal('valor_unitario', 15, 2)->after('unidade_medida');
            }
            if (!Schema::hasColumn('produtos_comprados', 'valor_total')) {
                $table->decimal('valor_total', 15, 2)->after('valor_unitario');
            }
            if (!Schema::hasColumn('produtos_comprados', 'obs')) {
                $table->text('obs')->nullable()->after('valor_total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produtos_comprados', function (Blueprint $table) {
            $columns = ['despesa_id', 'produto_id', 'quantidade', 'unidade_medida', 'valor_unitario', 'valor_total', 'obs'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('produtos_comprados', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
