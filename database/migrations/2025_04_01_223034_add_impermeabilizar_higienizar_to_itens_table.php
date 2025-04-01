<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('items', function (Blueprint $table) {
        $table->boolean('impermeabilizar')->default(false);
        $table->string('impermeabilizar_observacao')->nullable();
        $table->boolean('higienizar')->default(false);
        $table->string('higienizar_observacao')->nullable();
    });
}

public function down()
{
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn(['impermeabilizar', 'impermeabilizar_observacao', 'higienizar', 'higienizar_observacao']);
    });
}

};
