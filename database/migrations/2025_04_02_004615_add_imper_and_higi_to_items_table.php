<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImperAndHigiToItemsTable extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('imper')->default(false); // Campo para impermeabilização
            $table->text('imper_obs')->nullable(); // Campo para observação de impermeabilização
            $table->boolean('higi')->default(false); // Campo para higienização
            $table->text('higi_obs')->nullable(); // Campo para observação de higienização
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['imper', 'imper_obs', 'higi', 'higi_obs']);
        });
    }
}
