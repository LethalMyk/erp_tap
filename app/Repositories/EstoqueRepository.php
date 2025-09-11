<?php

namespace App\Repositories;

use App\Models\Estoque;

class EstoqueRepository
{
    protected $model;

    public function __construct(Estoque $model)
    {
        $this->model = $model;
    }

    /**
     * Retorna todos os produtos em estoque
     */
    public function all()
    {
        return $this->model->with('produto')->get();
    }

    /**
     * Retorna todos os produtos disponíveis (quantidade > 0)
     */
    public function allDisponiveis()
    {
        return $this->model->with('produto')
            ->where('quantidade_disponivel', '>', 0)
            ->get();
    }

    /**
     * Cria um novo registro de estoque
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Atualiza a quantidade disponível de um produto no estoque
     */
    public function updateQuantidade(int $produtoId, float $quantidade)
    {
        $estoque = $this->model->where('produto_id', $produtoId)->firstOrFail();
        $estoque->quantidade_disponivel = $quantidade;
        $estoque->save();
        return $estoque;
    }

    /**
     * Busca estoque por produto
     */
    public function findByProdutoId(int $produtoId)
    {
        return $this->model->where('produto_id', $produtoId)->first();
    }
}
