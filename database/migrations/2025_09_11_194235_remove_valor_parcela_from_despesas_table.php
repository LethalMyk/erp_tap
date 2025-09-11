<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            if (Schema::hasColumn('despesas', 'valor_parcela')) {
                $table->dropColumn('valor_parcela');
            }
        });
    }

    public function down(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            $table->decimal('valor_parcela', 15, 2)->nullable()->after('numero_parcela');
        });
    }
};
