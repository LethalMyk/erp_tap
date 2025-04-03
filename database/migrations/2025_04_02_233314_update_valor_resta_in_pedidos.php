<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Pedido;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->decimal('valorResta', 10, 2)->default(0)->change();
        });

        // Atualizar os pedidos existentes para garantir que valorResta seja igual a valor
        Pedido::query()->update(['valorResta' => \DB::raw('valor')]);
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->decimal('valorResta', 10, 2)->default(0)->change();
        });
    }
};
