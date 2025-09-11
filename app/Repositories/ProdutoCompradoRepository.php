<?php

namespace App\Repositories;

use App\Models\ProdutoComprado;

class ProdutoCompradoRepository
{
    protected ProdutoComprado $model;

    public function __construct(ProdutoComprado $produtoComprado)
    {
        $this->model = $produtoComprado;
    }

    public function find(int $id): ?ProdutoComprado
    {
        return $this->model->find($id);
    }

    public function create(array $data): ProdutoComprado
    {
        return $this->model->create($data);
    }

    public function update(ProdutoComprado $produto, array $data): ProdutoComprado
    {
        $produto->update($data);
        return $produto;
    }

    public function firstOrNew(array $attributes, array $values = []): ProdutoComprado
    {
        return $this->model->firstOrNew($attributes, $values);
    }

    public function delete(ProdutoComprado $produto): void
    {
        $produto->delete();
    }
}
