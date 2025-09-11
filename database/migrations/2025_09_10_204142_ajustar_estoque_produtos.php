<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estoque', function (Blueprint $table) {
            // Remove quantidade_disponivel pois agora é calculada pelos movimentos
            if (Schema::hasColumn('estoque', 'quantidade_disponivel')) {
                $table->dropColumn('quantidade_disponivel');
            }

            // Ajustes opcionais nos campos existentes
            $table->string('localizacao')->nullable()->change();
            $table->integer('nivel_medio')->nullable()->change();
            $table->integer('quantidade_minima')->nullable()->change();
        });

        Schema::table('produtos', function (Blueprint $table) {
            // Ajuste opcional, garante que categoria e descricao possam ser nulas
            $table->string('categoria')->nullable()->change();
            $table->text('descricao')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('estoque', function (Blueprint $table) {
            $table->integer('quantidade_disponivel')->default(0);
        });

        Schema::table('produtos', function (Blueprint $table) {
            $table->string('categoria')->nullable(false)->change();
            $table->text('descricao')->nullable(false)->change();
        });
    }
};
