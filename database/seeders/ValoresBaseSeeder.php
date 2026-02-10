<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ValorBase;

class ValoresBaseSeeder extends Seeder
{
    public function run(): void
    {
        ValorBase::truncate(); // limpa antes de inserir

        ValorBase::insert([
            [
                'tipo' => 'item',
                'nome' => '2 lug',
                'valor' => 500.00,
                'unidade' => null,
            ],
            [
                'tipo' => 'tecido',
                'nome' => 'CHOCO 03 - IDEALE',
                'valor' => 25.00,
                'unidade' => null,
            ],
            [
                'tipo' => 'espuma',
                'nome' => 'NOVA D-33',
                'valor' => 5.00,
                'unidade' => 'MT',
            ],
            [
                'tipo' => 'enchimento',
                'nome' => 'Soft Novo Total',
                'valor' => 5.00,
                'unidade' => 'Peça',
            ],
            [
                'tipo' => 'ferragem',
                'nome' => 'PÉS CANTO 6CM',
                'valor' => 7.00,
                'unidade' => 'UN',
            ],
            [
                'tipo' => 'estrutura',
                'nome' => 'Veneno de Cupim - Aplicar',
                'valor' => 150.00,
                'unidade' => 'Peça',
            ],
            [
                'tipo' => 'modelo',
                'nome' => '3x2 Fixo - Facil',
                'valor' => 1000.00,
                'unidade' => null,
            ],
              [
                'tipo' => 'modelo',
                'nome' => '3x2 Fixo - Medio',
                'valor' => 2000.00,
                'unidade' => null,
            ],
              [
                'tipo' => 'modelo',
                'nome' => '3x2 Fixo - Dificil',
                'valor' => 3000.00,
                'unidade' => null,
            ],
        ]);
    }
}
