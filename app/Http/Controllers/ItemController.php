<?php

// Controller - app/Http/Controllers/ItemController.php
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Pedido;
use App\Models\Terceirizada;

use Illuminate\Http\Request;


class ItemController extends Controller {
    public function index() {
        $items = Item::all();
        return view('items.index', compact('items'));
    }

public function create()
{
    $pedidos = Pedido::all(); // Buscar todos os pedidos existentes
    return view('items.create', compact('pedidos'));
}


public function store(Request $request)
{
    $request->validate([
        'nomeItem' => 'required|string',
        'material' => 'required|string',
        'metragem' => 'required|numeric',
        'especifi' => 'nullable|string',
        'pedido_id' => 'required|exists:pedidos,id', // Garante que pedido_id está sendo enviado
    ]);

    Item::create($request->all());

    return redirect()->route('items.index')->with('success', 'Item cadastrado com sucesso!');
}



    public function show(Item $item) {
        return view('items.show', compact('item'));
    }

    public function edit(Item $item) {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item) {
        $request->validate([
            'nomeItem' => 'required',
            'material' => 'required',
            'metragem' => 'required|numeric',
        ]);
        $item->update($request->all());
        return redirect()->route('items.index')->with('success', 'Item atualizado com sucesso!');
    }

    public function destroy(Item $item) {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item excluído com sucesso!');
    }
}