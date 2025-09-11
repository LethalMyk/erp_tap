<?php

namespace App\Services;

use App\Repositories\EstoqueRepository;
use Illuminate\Support\Facades\Validator;

class EstoqueService
{
    protected $repository;

    public function __construct(EstoqueRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Atualiza a quantidade disponível de um produto no estoque
     */
    public function atualizarQuantidade($produtoId, $quantidade)
    {
        if ($quantidade < 0) {
            throw new \InvalidArgumentException('A quantidade não pode ser negativa.');
        }

        return $this->repository->updateQuantidade($produtoId, $quantidade);
    }

    /**
     * Cria um novo produto no estoque
     */
    public function criarProduto(array $data)
    {
        // Validação das regras de negócio
        $validator = Validator::make($data, [
            'nome' => 'required|string|max:255',
            'quantidade_disponivel' => 'required|integer|min:0',
            'unidade_medida' => 'required|string|max:50',
            'categoria' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        return $this->repository->create($validator->validated());
    }

    /**
     * Lista todos os produtos disponíveis no estoque
     */
    public function listarProdutos()
    {
        return $this->repository->allDisponiveis();
    }
}
