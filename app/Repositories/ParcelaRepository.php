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

    /**
     * Busca uma parcela pelo ID
     */
    public function find(int $id): ?Parcela
    {
        return $this->model->find($id);
    }

    /**
     * Cria uma nova parcela
     */
    public function create(array $data): Parcela
    {
        return $this->model->create($data);
    }

    /**
     * Atualiza uma parcela existente
     */
    public function update(Parcela $parcela, array $data): Parcela
    {
        $parcela->update($data);
        return $parcela;
    }

    /**
     * Exclui uma parcela
     */
    public function delete(Parcela $parcela): void
    {
        $parcela->delete();
    }

    /**
     * Retorna todas as parcelas de uma despesa
     */
    public function findByDespesa(int $despesaId)
    {
        return $this->model->where('despesa_id', $despesaId)->orderBy('numero_parcela')->get();
    }

    /**
     * Retorna parcelas com filtros opcionais (status, data, forma de pagamento)
     */
    public function all(array $filters = [])
    {
        $query = $this->model->query();

        if (!empty($filters['despesa_id'])) {
            $query->where('despesa_id', $filters['despesa_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['forma_pagamento'])) {
            $query->where('forma_pagamento', $filters['forma_pagamento']);
        }
        if (!empty($filters['data_inicio'])) {
            $query->whereDate('data_vencimento', '>=', $filters['data_inicio']);
        }
        if (!empty($filters['data_fim'])) {
            $query->whereDate('data_vencimento', '<=', $filters['data_fim']);
        }

        return $query->orderBy('data_vencimento')->paginate($filters['per_page'] ?? 10);
    }
}
