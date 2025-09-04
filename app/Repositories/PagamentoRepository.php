<?php

namespace App\Repositories;

use App\Models\Pagamento;

class PagamentoRepository
{
    public function all()
    {
        return Pagamento::with('pedido.cliente')->get();
    }

    public function find($id)
    {
        return Pagamento::with('pedido.cliente')->find($id);
    }

    public function create(array $dados)
    {
        return Pagamento::create($dados);
    }

    public function update(Pagamento $pagamento, array $dados)
    {
        return $pagamento->update($dados);
    }

    public function delete(Pagamento $pagamento)
    {
        return $pagamento->delete();
    }

    public function sumPagamentosRegistrados($pedidoId)
    {
        return Pagamento::where('pedido_id', $pedidoId)
                        ->where('status', 'PAGAMENTO REGISTRADO')
                        ->sum('valor');
    }
}
