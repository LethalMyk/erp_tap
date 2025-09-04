<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            // coluna só com a data do registro
            $table->date('data')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('despesas', function (Blueprint $table) {
            $table->dropColumn('data');
        });
    }
};
