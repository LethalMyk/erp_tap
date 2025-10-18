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

    // 🔹 Filtros padrão
    if (!empty($filters['descricao'])) {
        $query->where('descricao', 'like', '%' . $filters['descricao'] . '%');
    }

    if (!empty($filters['categoria'])) {
        $query->where('categoria', $filters['categoria']);
    }

    if (!empty($filters['forma_pagamento'])) {
        $query->where('forma_pagamento', $filters['forma_pagamento']);
    }

    if (!empty($filters['status'])) {
        $query->where('status', $filters['status']);
    }

    // 🔹 Filtro por data de vencimento das parcelas
    if (!empty($filters['parcela_data_inicio']) || !empty($filters['parcela_data_fim'])) {
        $start = $filters['parcela_data_inicio'] ?? null;
        $end = $filters['parcela_data_fim'] ?? null;

        $query->whereHas('parcelas', function($q) use ($start, $end) {
            if ($start && $end) {
                $q->whereBetween('data_vencimento', [$start, $end]);
            } elseif ($start) {
                $q->whereDate('data_vencimento', '>=', $start);
            } elseif ($end) {
                $q->whereDate('data_vencimento', '<=', $end);
            }
        });
    }

    // 🔹 Ordenação
    $allowedSorts = ['created_at', 'valor_total', 'data_vencimento', 'data'];
    $sort = $filters['sort'] ?? 'created_at';
    $direction = strtolower($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

    if (in_array($sort, $allowedSorts)) {
        if ($sort === 'data_vencimento') {
            // Ordenar pela PRÓXIMA parcela a vencer (ignora as já pagas ou vencidas)
            $table = $this->model->getTable();
            $query->orderByRaw("(
                select min(p.data_vencimento)
                from parcelas p
                where p.despesa_id = {$table}.id
                and (p.status is null or p.status != 'PAGO')
            ) {$direction}");
        } else {
            $query->orderBy($sort, $direction);
        }
    } else {
        $query->orderBy('created_at', 'desc');
    }

    // 🔹 Eager loading e paginação
    return $query->with('parcelas', 'produtosComprados.produto')->paginate($filters['per_page'] ?? 10);
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
