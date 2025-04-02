<?php

namespace App\Http\Controllers;

use App\Models\Terceirizada;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Models\Pedido;


class TerceirizadaController extends Controller
{
    public function index()
    {
        $terceirizadas = Terceirizada::with('item')->get();
        return view('terceirizadas.index', compact('terceirizadas'));
        
    }
public function create()
{
    $pedidos = Pedido::all(); // Buscar todos os pedidos existentes
    return view('terceirizadas.create', compact('pedidos'));
}


public function store(Request $request)
{
    $request->validate([
        'tipoServico' => 'required|string|max:255',
        'obs' => 'nullable|string',
        'item_id' => 'required|exists:items,id',
        'pedido_id' => 'required|exists:pedidos,id', // Adicione esta validação
    ]);

    Terceirizada::create($request->all());

    return redirect()->route('terceirizadas.index')->with('success', 'Serviço terceirizado cadastrado com sucesso!');
}

   public function show(Terceirizada $terceirizada)
{
    $terceirizada->load(['item.pedido']); // Carregar o Pedido do Item
    return view('terceirizadas.show', compact('terceirizada'));
}



public function edit($id)
{
    $terceirizada = Terceirizada::findOrFail($id);
    $pedidos = Pedido::all(); // Busca todos os pedidos
    $pedidoSelecionado = Pedido::find($terceirizada->pedido_id); // Obtém o pedido associado

    return view('terceirizadas.edit', compact('terceirizada', 'pedidos', 'pedidoSelecionado'));
}






    public function update(Request $request, Terceirizada $terceirizada)
    {
        $request->validate([
            'tipoServico' => 'required|string|max:255',
            'obs' => 'nullable|string',
            'item_id' => 'required|exists:items,id',
        ]);

        $terceirizada->update($request->all());
        return redirect()->route('terceirizadas.index')->with('success', 'Serviço terceirizado atualizado com sucesso!');
    }

    public function destroy(Terceirizada $terceirizada)
    {
        $terceirizada->delete();
        return redirect()->route('terceirizadas.index')->with('success', 'Serviço terceirizado removido!');
    }
    public function getItems($pedido_id)
{
    $items = Item::where('pedido_id', $pedido_id)->get(['id', 'nomeItem as descricao']); 
    return response()->json($items);
}



}
