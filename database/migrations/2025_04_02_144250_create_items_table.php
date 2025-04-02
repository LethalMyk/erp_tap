<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->string('nomeItem');
            $table->string('material');
            $table->decimal('metragem', 8, 2);
            $table->text('especifi')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('items');
    }
};