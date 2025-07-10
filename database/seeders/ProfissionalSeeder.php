<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProfissionalSeeder extends Seeder
{
    public function run()
    {
        $profissionais = [
            ['nome' => 'André', 'cargo' => 'Tapeceiro'],
            ['nome' => 'Samuel', 'cargo' => 'Tapeceiro'],
            ['nome' => 'Paulo', 'cargo' => 'Tapeceiro'],
            ['nome' => 'Adailton', 'cargo' => 'AjudanteTap'],
            ['nome' => 'Distribuir', 'cargo' => ''],
        ];

        foreach ($profissionais as $p) {
            DB::table('profissional')->insert([
                'nome' => $p['nome'],
                'cargo' => $p['cargo'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
