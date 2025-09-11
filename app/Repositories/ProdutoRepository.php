<?php

namespace App\Repositories;

use App\Models\Produto;

class ProdutoRepository
{
    protected Produto $model;

    public function __construct(Produto $produto)
    {
        $this->model = $produto;
    }

    public function find(int $id): ?Produto
    {
        return $this->model->find($id);
    }

    public function create(array $data): Produto
    {
        return $this->model->create($data);
    }

    public function firstOrCreate(array $attributes, array $values = []): Produto
    {
        return $this->model->firstOrCreate($attributes, $values);
    }
}
