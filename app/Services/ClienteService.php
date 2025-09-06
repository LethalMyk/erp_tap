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
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listarTodos()
    {
        return $this->repository->all();
    }

    /**
     * Busca cliente por ID
     *
     * @param int $id
     * @return Cliente|null
     */
    public function buscarPorId(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Cria um novo cliente
     *
     * @param array $dados
     * @return Cliente
     */
    public function criar(array $dados)
    {
        return $this->repository->create($dados);
    }

    /**
     * Atualiza um cliente existente
     *
     * @param Cliente $cliente
     * @param array $dados
     * @return Cliente
     */
    public function atualizar(Cliente $cliente, array $dados)
    {
        $this->repository->update($cliente, $dados);
        return $cliente;
    }

    /**
     * Deleta um cliente
     *
     * @param Cliente $cliente
     * @return bool
     */
    public function deletar(Cliente $cliente)
    {
        return $this->repository->delete($cliente);
    }

    /**
     * Cria ou atualiza um cliente com base nos dados fornecidos
     * 
     * Se o ID existir, atualiza o cliente. Caso contrário, cria um novo.
     *
     * @param array $dados
     * @return Cliente
     */
    public function criarOuAtualizarCliente(array $dados)
    {
        // Se ID for enviado, tenta atualizar
        if (!empty($dados['id'])) {
            $cliente = $this->repository->find($dados['id']);
            if ($cliente) {
                return $this->atualizar($cliente, $dados);
            }
        }

        // Se email existir, evita duplicação
        if (!empty($dados['email'])) {
            $clienteExistente = Cliente::where('email', $dados['email'])->first();
            if ($clienteExistente) {
                return $this->atualizar($clienteExistente, $dados);
            }
        }

        // Cria novo cliente se não existir
        return $this->criar($dados);
    }
}
