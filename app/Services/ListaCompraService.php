<?php

namespace App\Services;

use App\Models\ListaCompra;

class ListaCompraService
{
    /**
     * Cria um novo item de lista de compra
     */
    public function criar(array $data): ListaCompra
    {
        return ListaCompra::create($data);
    }

    /**
     * Atualiza situação, metragem e fornecedor de um item de compra
     */
    public function atualizarSituacao(int $id, array $data): ListaCompra
    {
        $compra = ListaCompra::findOrFail($id);

        // Atualiza somente os campos enviados
        if (isset($data['situacao'])) {
            $compra->situacao = $data['situacao'];
        }
        if (isset($data['metragem'])) {
            $compra->metragem = $data['metragem'];
        }
        if (isset($data['fornecedor'])) {
            $compra->fornecedor = $data['fornecedor'];
        }

        $compra->save();

        return $compra;
    }

    /**
     * Lista todos os itens de compra com pedido e estoque carregados (eager loading)
     */
    public function listarTodosComEstoque()
{
    return ListaCompra::with(['pedido', 'estoque.produto'])->orderBy('situacao')->get();
}


    /**
     * Lista todos os itens de compra
     */
    public function listarTodos()
    {
        return ListaCompra::orderBy('situacao')->get();
    }

    /**
     * Lista itens de compra por situação específica
     */
    public function listarPorSituacao(string $situacao)
    {
        return ListaCompra::where('situacao', $situacao)->get();
    }

    
}
