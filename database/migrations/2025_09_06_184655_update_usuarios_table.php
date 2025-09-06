<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('profissional')->insert([
            ['nome' => 'Paulo', 'cargo' => 'Tapeceiro'],
            ['nome' => 'Marco', 'cargo' => 'Tapeceiro'],
            ['nome' => 'Andre', 'cargo' => 'Tapeceiro'],
            ['nome' => 'Jose', 'cargo' => 'Tapeceiro'],
            ['nome' => 'Adailton', 'cargo' => 'Auxiliar Tapeceiro'],
            ['nome' => 'Distribuir', 'cargo' => 'Tapeceiro'],

        ]);
    }

    public function down(): void
    {
        DB::table('profissional')->whereIn('nome', ['Paulo','Marco','Andre','Jose','Adailton'])->delete();
    }
};
