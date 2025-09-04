<?php

namespace App\Services;

use App\Repositories\TerceirizadaRepository;
use App\Models\Terceirizada;

class TerceirizadaService
{
    protected $repository;

    public function __construct(TerceirizadaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listar()
    {
        return $this->repository->all();
    }

    public function criar(array $dados)
    {
        // Aqui você pode adicionar lógica de negócio, como cálculos, notificações etc.
        return $this->repository->create($dados);
    }

    public function atualizar(Terceirizada $terceirizada, array $dados)
    {
        return $this->repository->update($terceirizada, $dados);
    }

    public function remover(Terceirizada $terceirizada)
    {
        return $this->repository->delete($terceirizada);
    }
}
