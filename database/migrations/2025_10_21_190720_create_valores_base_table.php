<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('valores_base', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['item', 'tecido', 'espuma', 'enchimento', 'adicional']);
            $table->string('nome');
            $table->decimal('valor', 10, 2);
            $table->string('unidade')->nullable(); // Ex: 'por unidade', 'por conjunto'
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('valores_base');
    }
};
