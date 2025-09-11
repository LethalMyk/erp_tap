<?php

namespace App\Repositories;

use App\Models\MovimentoEstoque;

class MovimentoEstoqueRepository
{
    protected MovimentoEstoque $model;

    public function __construct(MovimentoEstoque $movimento)
    {
        $this->model = $movimento;
    }

    public function find(int $id): ?MovimentoEstoque
    {
        return $this->model->find($id);
    }

    public function create(array $data): MovimentoEstoque
    {
        return $this->model->create($data);
    }

    public function update(MovimentoEstoque $movimento, array $data): MovimentoEstoque
    {
        $movimento->update($data);
        return $movimento;
    }

    public function delete(MovimentoEstoque $movimento): void
    {
        $movimento->delete();
    }

    public function findByEstoque(int $estoqueId)
    {
        return $this->model->where('estoque_id', $estoqueId)->get();
    }
}
