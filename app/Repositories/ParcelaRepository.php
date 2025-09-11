<?php

namespace App\Repositories;

use App\Models\Parcela;

class ParcelaRepository
{
    protected Parcela $model;

    public function __construct(Parcela $parcela)
    {
        $this->model = $parcela;
    }

    public function find(int $id): ?Parcela
    {
        return $this->model->find($id);
    }

    public function create(array $data): Parcela
    {
        return $this->model->create($data);
    }

    public function update(Parcela $parcela, array $data): Parcela
    {
        $parcela->update($data);
        return $parcela;
    }

    public function delete(Parcela $parcela): void
    {
        $parcela->delete();
    }

    public function findByDespesa(int $despesaId)
    {
        return $this->model->where('despesa_id', $despesaId)->get();
    }
}
