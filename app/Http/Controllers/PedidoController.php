<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    // Método para exibir a lista de pedidos
    public function index()
    {
        $pedidos = Pedido::with('cliente')->get();
        return view('pedidos.index', compact('pedidos'));
    }

    // Método para exibir o formulário de criação de pedido
    public function create()
    {
        $clientes = Cliente::all();
        return view('pedidos.create', compact('clientes'));
    }

    // Método para armazenar um novo pedido
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'qntItens' => 'required|integer',
            'data' => 'required|date',
            'valor' => 'required|numeric',
            'status' => 'required|string',
            'obs' => 'nullable|string',
            'prazo' => 'required|date',
        ]);

        Pedido::create($request->all());

        return redirect()->route('pedidos.index')->with('success', 'Pedido criado com sucesso!');
    }

    // Método para exibir o formulário de edição de pedido
    public function edit(Pedido $pedido)
    {
        $clientes = Cliente::all();
        return view('pedidos.edit', compact('pedido', 'clientes'));
    }

    // Método para atualizar um pedido
    public function update(Request $request, Pedido $pedido)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'qntItens' => 'required|integer',
            'data' => 'required|date',
            'valor' => 'required|numeric',
            'status' => 'required|string',
            'obs' => 'nullable|string',
            'prazo' => 'required|date',
        ]);

        $pedido->update($request->all());

        return redirect()->route('pedidos.index')->with('success', 'Pedido atualizado com sucesso!');
    }

    // Método para exibir os detalhes de um pedido
    public function show(Pedido $pedido)
    {
        return view('pedidos.show', compact('pedido'));
    }
}
