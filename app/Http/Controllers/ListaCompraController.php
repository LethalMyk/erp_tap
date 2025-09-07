<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ListaCompraService;
use App\Models\ListaCompra;

class ListaCompraController extends Controller
{
    protected ListaCompraService $service;

    public function __construct(ListaCompraService $service)
    {
        $this->service = $service;
    }

    /**
     * Lista todos os itens de compra com estoque e pedido carregados
     */
   public function index()
{
    $compras = $this->service->listarTodosComEstoque();
    return view('estoque.listacompra', compact('compras'));
}


    /**
     * Atualiza situação, metragem e fornecedor de um item de compra
     */
    public function atualizarSituacao(Request $request, ListaCompra $item)
    {
        $data = $request->only(['situacao', 'metragem', 'fornecedor']);

        $request->validate([
            'situacao'   => 'nullable|string',
            'metragem'   => 'nullable|numeric|min:0',
            'fornecedor' => 'nullable|string|max:255',
        ]);

        $this->service->atualizarSituacao($item->id, $data);

        return redirect()->back()->with('success', 'Atualização realizada com sucesso!');
    }
}
