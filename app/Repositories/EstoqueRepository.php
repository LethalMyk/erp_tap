<?php

namespace App\Repositories;

use App\Models\Estoque;

class EstoqueRepository
{
    public function allDisponiveis()
    {
        return Estoque::with('produto')
            ->where('quantidade_disponivel', '>', 0)
            ->orderBy('id', 'asc')
            ->get();
    }

    public function updateQuantidade($estoqueId, $quantidade)
    {
        return \DB::table('estoque')
            ->where('id', $estoqueId) // aqui usamos o ID da tabela estoque
            ->update(['quantidade_disponivel' => $quantidade]);
    }
}
