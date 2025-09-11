<?php

namespace App\Repositories;

use App\Models\Estoque;

class EstoqueRepository
{
    /**
     * Retorna todos os produtos com quantidade maior que zero
     */
    public function allDisponiveis()
    {
        return Estoque::with('produto')
            ->where('quantidade_disponivel', '>', 0)
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Atualiza a quantidade de um produto no estoque
     */
    public function updateQuantidade($estoqueId, $quantidade)
    {
        $estoque = Estoque::findOrFail($estoqueId);
        $estoque->quantidade_disponivel = $quantidade;
        $estoque->save();

        return $estoque;
    }

    /**
     * Cria um novo produto no estoque
     */
    public function create(array $data)
    {
        return Estoque::create([
            'nome' => $data['nome'],
            'categoria' => $data['categoria'] ?? null,
            'quantidade_disponivel' => $data['quantidade_disponivel'] ?? 0,
            'unidade_medida' => $data['unidade_medida'],
        ]);
    }

    /**
     * Retorna um produto pelo ID
     */
    public function findById($estoqueId)
    {
        return Estoque::findOrFail($estoqueId);
    }
}
