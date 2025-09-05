<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class EstoqueRepository
{
    /**
     * Retorna todos os produtos disponíveis no estoque
     *
     * @return \Illuminate\Support\Collection
     */
    public function allDisponiveis()
    {
        return DB::table('estoque')
            ->join('produtos', 'produtos.id', '=', 'estoque.produto_id')
            ->select(
                'produtos.id',
                'produtos.nome',
                'produtos.categoria',
                'produtos.unidade_medida',   // Adiciona a unidade de medida
                'estoque.quantidade_disponivel',
                'estoque.valor_unitario'     // Adiciona valor unitário
            )
            ->where('estoque.quantidade_disponivel', '>', 0)
            ->orderBy('produtos.nome', 'asc')
            ->get();
    }
}
