<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('terceirizadas', function (Blueprint $table) {
            $table->id();
            $table->string('tipoServico');
            $table->text('obs')->nullable();
            $table->foreignId('item_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('terceirizadas');
    }
};
