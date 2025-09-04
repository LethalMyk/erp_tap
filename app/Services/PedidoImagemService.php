<?php

namespace App\Services;

use App\Repositories\PedidoImagemRepository;
use App\Models\PedidoImagem;

class PedidoImagemService
{
    protected $repository;

    public function __construct(PedidoImagemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listar()
    {
        return $this->repository->all();
    }

    public function listarPorPedido(int $pedidoId)
    {
        return $this->repository->findByPedido($pedidoId);
    }

    public function criar(array $dados)
    {
        return $this->repository->create($dados);
    }

    public function atualizar(PedidoImagem $pedidoImagem, array $dados)
    {
        return $this->repository->update($pedidoImagem, $dados);
    }

    public function remover(PedidoImagem $pedidoImagem)
    {
        return $this->repository->delete($pedidoImagem);
    }
}
