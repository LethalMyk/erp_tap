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

    /**
     * Busca uma despesa pelo ID com parcelas e produtos comprados
     */
    public function find(int $id): ?Despesa
    {
        return $this->model->with('parcelas', 'produtosComprados.produto')->find($id);
    }

    /**
     * Retorna todas as despesas com filtros opcionais e paginação
     */
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
        if (!empty($filters['data_inicio'])) {
            $query->whereDate('data', '>=', $filters['data_inicio']);
        }
        if (!empty($filters['data_fim'])) {
            $query->whereDate('data', '<=', $filters['data_fim']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Cria uma nova despesa
     */
    public function create(array $data): Despesa
    {
        return $this->model->create($data);
    }

    /**
     * Atualiza uma despesa existente
     */
    public function update(Despesa $despesa, array $data): Despesa
    {
        $despesa->update($data);
        return $despesa;
    }

    /**
     * Exclui uma despesa
     */
    public function delete(Despesa $despesa): void
    {
        $despesa->delete();
    }
}
