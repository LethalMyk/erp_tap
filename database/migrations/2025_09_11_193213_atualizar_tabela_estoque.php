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
        Schema::table('estoque', function (Blueprint $table) {
            if (!Schema::hasColumn('estoque', 'produto_id')) {
                $table->unsignedBigInteger('produto_id')->after('id');
            }
            if (!Schema::hasColumn('estoque', 'localizacao')) {
                $table->string('localizacao')->nullable()->after('produto_id');
            }
            if (!Schema::hasColumn('estoque', 'nivel_medio')) {
                $table->decimal('nivel_medio', 15, 2)->nullable()->after('localizacao');
            }
            if (!Schema::hasColumn('estoque', 'quantidade_minima')) {
                $table->decimal('quantidade_minima', 15, 2)->nullable()->after('nivel_medio');
            }
            if (!Schema::hasColumn('estoque', 'quantidade_disponivel')) {
                $table->decimal('quantidade_disponivel', 15, 2)->default(0)->after('quantidade_minima');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estoque', function (Blueprint $table) {
            $columns = ['produto_id', 'localizacao', 'nivel_medio', 'quantidade_minima', 'quantidade_disponivel'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('estoque', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
