<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parcelas', function (Blueprint $table) {
            $table->string('forma_pagamento', 30)->change();
        });
    }

    public function down(): void
    {
        Schema::table('parcelas', function (Blueprint $table) {
            $table->string('forma_pagamento', 10)->change(); // valor antigo, ajuste se necessário
        });
    }
};
