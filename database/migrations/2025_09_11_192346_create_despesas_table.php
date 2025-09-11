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
        Schema::table('produtos', function (Blueprint $table) {
            if (!Schema::hasColumn('produtos', 'nome')) {
                $table->string('nome')->after('id');
            }
            if (!Schema::hasColumn('produtos', 'unidade_medida')) {
                $table->string('unidade_medida')->nullable()->after('nome');
            }
            if (!Schema::hasColumn('produtos', 'categoria')) {
                $table->string('categoria')->nullable()->after('unidade_medida');
            }
            if (!Schema::hasColumn('produtos', 'sub_categoria')) {
                $table->string('sub_categoria')->nullable()->after('categoria');
            }
            if (!Schema::hasColumn('produtos', 'descricao')) {
                $table->text('descricao')->nullable()->after('sub_categoria');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            $columns = ['nome', 'unidade_medida', 'categoria', 'sub_categoria', 'descricao'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('produtos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
