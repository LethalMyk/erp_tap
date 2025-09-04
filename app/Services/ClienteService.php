<?php

namespace App\Services;

use App\Models\Cliente;
use App\Repositories\ClienteRepository;

class ClienteService
{
    protected $repository;

    public function __construct(ClienteRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Lista todos os clientes
     */
    public function listarTodos()
    {
        return $this->repository->all();
    }

    /**
     * Busca cliente por ID
     */
    public function buscarPorId($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Cria um novo cliente
     */
    public function criar(array $dados)
    {
        return $this->repository->create($dados);
    }

    /**
     * Atualiza um cliente existente
     */
    public function atualizar(Cliente $cliente, array $dados)
    {
        return $this->repository->update($cliente, $dados);
    }

    /**
     * Deleta um cliente
     */
    public function deletar(Cliente $cliente)
    {
        return $this->repository->delete($cliente);
    }

    /**
     * Cria ou atualiza um cliente com base nos dados fornecidos
     */
    public function criarOuAtualizarCliente(array $dados)
    {
        if (!empty($dados['id'])) {
            $cliente = $this->repository->find($dados['id']);
            if ($cliente) {
                return $this->atualizar($cliente, $dados);
            }
        }

        return $this->criar($dados);
    }
}
