<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('terceirizadas', function (Blueprint $table) {
        $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terceirizadas', function (Blueprint $table) {
            //
        });
    }
};
