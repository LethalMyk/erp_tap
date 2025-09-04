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
    Schema::table('parcelas', function (Blueprint $table) {
        $table->string('descricao')->nullable()->after('valor_parcela');
    });
}

public function down(): void
{
    Schema::table('parcelas', function (Blueprint $table) {
        $table->dropColumn('descricao');
    });
}

};
