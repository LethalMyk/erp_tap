<?php

namespace App\Repositories;

use App\Models\Produto;

class ProdutoRepository
{
    protected Produto $model;

    public function __construct(Produto $produto)
    {
        $this->model = $produto;
    }

    /**
     * Retorna um produto pelo ID
     */
    public function find(int $id): ?Produto
    {
        return $this->model->find($id);
    }

    /**
     * Cria um novo produto
     */
    public function create(array $data): Produto
    {
        return $this->model->create($data);
    }

    /**
     * Cria ou retorna o primeiro produto que corresponda aos atributos
     */
    public function firstOrCreate(array $attributes, array $values = []): Produto
    {
        return $this->model->firstOrCreate($attributes, $values);
    }

    /**
     * Busca produto pelo nome
     */
    public function findByName(string $nome): ?Produto
    {
        return $this->model->where('nome', $nome)->first();
    }

    /**
     * Lista produtos com filtros opcionais (nome, categoria)
     */
    public function all(array $filters = [])
    {
        $query = $this->model->query();

        if (!empty($filters['nome'])) {
            $query->where('nome', 'like', '%' . $filters['nome'] . '%');
        }

        if (!empty($filters['categoria'])) {
            $query->where('categoria', $filters['categoria']);
        }

        return $query->orderBy('nome')->get();
    }

    /**
     * Atualiza um produto
     */
    public function update(Produto $produto, array $data): Produto
    {
        $produto->update($data);
        return $produto;
    }

    /**
     * Exclui um produto
     */
    public function delete(Produto $produto): void
    {
        $produto->delete();
    }
}
