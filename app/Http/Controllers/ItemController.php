<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Item;
use App\Services\ItemService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    protected $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function index()
    {
        $items = $this->itemService->listarTodos();
        return view('items.index', compact('items'));
    }

    public function create()
    {
        $pedidos = Pedido::all();
        return view('items.create', compact('pedidos'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nomeItem' => 'required|string',
            'material' => 'required|string',
            'metragem' => 'required|numeric',
            'especifi' => 'nullable|string',
            'pedido_id' => 'required|exists:pedidos,id',
            'terceirizadas' => 'nullable|array',
        ]);

        $item = $this->itemService->criar($dados);

        return redirect()->route('pedido.visualizar', $dados['pedido_id'])
                         ->with('success', 'Item cadastrado com sucesso!');
    }

    public function show($id)
    {
        $item = $this->itemService->buscarPorId($id);

        if (!$item) {
            return redirect()->route('items.index')->with('error', 'Item não encontrado.');
        }

        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $item->load('terceirizadas');
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $dados = $request->validate([
            'nomeItem' => 'required|string',
            'material' => 'required|string',
            'metragem' => 'required|numeric',
            'especifi' => 'nullable|string',
            'terceirizadas' => 'nullable|array',
        ]);

        $this->itemService->atualizar($item, $dados);

        return redirect()->back()->with('success', 'Item e serviços atualizados com sucesso!');
    }

    public function destroy(Item $item)
    {
        $this->itemService->deletar($item);
        return redirect()->route('items.index')->with('success', 'Item excluído com sucesso!');
    }
}
