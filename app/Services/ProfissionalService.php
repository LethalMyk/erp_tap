<?php

namespace App\Services;

use App\Repositories\ProfissionalRepository;
use App\Models\Profissional;

class ProfissionalService
{
    protected $repository;

    public function __construct(ProfissionalRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listar()
    {
        return $this->repository->all();
    }

    public function criar(array $dados)
    {
        return $this->repository->create($dados);
    }

    public function atualizar(Profissional $profissional, array $dados)
    {
        return $this->repository->update($profissional, $dados);
    }

    public function remover(Profissional $profissional)
    {
        return $this->repository->delete($profissional);
    }
}
