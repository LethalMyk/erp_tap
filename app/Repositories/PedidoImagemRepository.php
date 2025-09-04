<?php

namespace App\Repositories;

use App\Models\PedidoImagem;

class PedidoImagemRepository
{
    protected $model;

    public function __construct(PedidoImagem $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(PedidoImagem $pedidoImagem, array $data)
    {
        $pedidoImagem->update($data);
        return $pedidoImagem;
    }

    public function delete(PedidoImagem $pedidoImagem)
    {
        return $pedidoImagem->delete();
    }

    public function findByPedido(int $pedidoId)
    {
        return $this->model->where('pedido_id', $pedidoId)->get();
    }
}
