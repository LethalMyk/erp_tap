<?php

namespace App\Repositories;

use App\Models\Despesa;

class DespesaRepository
{
    protected Despesa $model;

    public function __construct(Despesa $despesa)
    {
        $this->model = $despesa;
    }

    public function find(int $id): ?Despesa
    {
        return $this->model->with('parcelas', 'produtosComprados.produto')->find($id);
    }

    public function all(array $filters = [])
    {
        $query = $this->model->query();

        if (!empty($filters['descricao'])) {
            $query->where('descricao', 'like', '%' . $filters['descricao'] . '%');
        }
        if (!empty($filters['categoria'])) {
            $query->where('categoria', $filters['categoria']);
        }
        if (!empty($filters['forma_pagamento'])) {
            $query->where('forma_pagamento', $filters['forma_pagamento']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 10);
    }

    public function create(array $data): Despesa
    {
        return $this->model->create($data);
    }

    public function update(Despesa $despesa, array $data): Despesa
    {
        $despesa->update($data);
        return $despesa;
    }

    public function delete(Despesa $despesa): void
    {
        $despesa->delete();
    }
}
