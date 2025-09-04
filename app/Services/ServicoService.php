<?php

namespace App\Services;

use App\Repositories\ServicoRepository;
use App\Models\Servico;

class ServicoService
{
    protected $repository;

    public function __construct(ServicoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listar()
    {
        return $this->repository->all();
    }

    public function criar(array $dados)
    {
        // Aqui pode adicionar lógica extra, notificações, regras de negócio, etc.
        return $this->repository->create($dados);
    }

    public function atualizar(Servico $servico, array $dados)
    {
        return $this->repository->update($servico, $dados);
    }

    public function remover(Servico $servico)
    {
        return $this->repository->delete($servico);
    }
}
