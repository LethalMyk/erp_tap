<?php

namespace App\Services;

use App\Repositories\EstoqueRepository;

class EstoqueService
{
    protected $repository;

    public function __construct(EstoqueRepository $repository)
    {
        $this->repository = $repository;
    }

    public function atualizarQuantidade($produtoId, $quantidade)
    {
        // Validação básica
        if ($quantidade < 0) {
            throw new \Exception('A quantidade não pode ser negativa.');
        }

        return $this->repository->updateQuantidade($produtoId, $quantidade);
    }
}
