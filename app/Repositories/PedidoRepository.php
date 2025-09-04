<?php

namespace App\Repositories;

use App\Models\Pedido;

class PedidoRepository
{
    protected $model;

    public function __construct(Pedido $pedido)
    {
        $this->model = $pedido;
    }

    /**
     * Retorna pedidos filtrados conforme parâmetros.
     */
    public function getFiltered(array $filters = [])
    {
        $query = $this->model->with(['cliente', 'imagens']);

        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (!empty($filters['cliente_nome'])) {
            $query->whereHas('cliente', fn($q) => $q->where('nome', 'like', '%' . $filters['cliente_nome'] . '%'));
        }

        if (!empty($filters['endereco'])) {
            $query->whereHas('cliente', fn($q) => $q->where('endereco', 'like', '%' . $filters['endereco'] . '%'));
        }

        if (!empty($filters['telefone'])) {
            $query->whereHas('cliente', fn($q) => $q->where('telefone', 'like', '%' . $filters['telefone'] . '%'));
        }

        if (!empty($filters['data'])) {
            $query->whereDate('data', $filters['data']);
        }

        if (!empty($filters['andamento'])) {
            $andamento = $filters['andamento'];
            if (is_array($andamento)) {
                $query->whereIn('andamento', $andamento);
            } else {
                $query->where('andamento', 'like', '%' . $andamento . '%');
            }
        }

        if (!empty($filters['tapeceiro'])) {
            $query->where('tapeceiro', 'like', '%' . $filters['tapeceiro'] . '%');
        }

        if (!empty($filters['mes'])) {
            $query->whereMonth('prazo', $filters['mes']);
        }

        // Ordenação customizada
        if (!empty($filters['custom_order'])) {
            $names = array_map('trim', explode(',', $filters['custom_order']));
            $names = array_map(fn($name) => addslashes($name), $names);

            $orderCases = [];
            foreach ($names as $index => $name) {
                $orderCases[] = "WHEN tapeceiro LIKE '%$name%' THEN $index";
            }

            $orderRaw = "CASE " . implode(' ', $orderCases) . " ELSE " . count($names) . " END";
            $query->orderByRaw($orderRaw);
        } else {
            $sortField = $filters['sort_field'] ?? 'id';
            $sortDirection = $filters['sort_direction'] ?? 'desc';
            $allowedFields = ['id', 'data', 'andamento', 'tapeceiro', 'prazo'];

            if (in_array($sortField, $allowedFields)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('id', 'desc');
            }
        }

        return $query->get();
    }

    /**
     * Retorna um pedido simples pelo ID
     */
    public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Retorna um pedido com todas as relações necessárias
     */
    public function findWithRelations(int $id)
    {
        return $this->model->with(['cliente', 'items.terceirizadas', 'pagamentos', 'imagens'])->findOrFail($id);
    }
}
