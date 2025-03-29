<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Item;
use App\Models\Pagamento;
use App\Models\Client; // Certifique-se de que o modelo Cliente está importado
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function index()
    {
        // Buscar todos os pedidos para exibir na visão
        $pedidos = Pedido::all();
        return view('pedidos.index', compact('pedidos'));
    }

    // Exibir o formulário de criação do pedido
    public function create()
    {
        return view('pedidos.create');
    }

    // Armazenar o pedido, itens, pagamento e o cliente
public function store(Request $request)
{
    $request->validate([
        'nome_cliente' => 'nullable|string|max:255', 
        'telefone_cliente' => 'nullable|string|max:15',
        'endereco_cliente' => 'nullable|string|max:255',
        'email_cliente' => 'nullable|string|email|max:255',
        'cpf_cliente' => 'nullable|string|max:14',
        'data' => 'required|date',
        'orcamento' => 'required|numeric',
        'status' => 'required|string',
        'prazo' => 'required|date',
        'data_retirada' => 'required|date',
        'obs' => 'nullable|string',
        'items' => 'required|array|min:1|max:10',
        'items.*.nome_item' => 'required|string',
        'items.*.material' => 'required|string',
        'items.*.metragem' => 'required|numeric',
        'items.*.especificacao' => 'nullable|string',
        'valor_pagamento' => 'required|numeric',
        'forma_pagamento' => 'required|string',
    ]);

    // Verificar se cliente_id foi enviado, se não, criar um novo cliente
    if (!$request->cliente_id) {
        // Criando o cliente
        $cliente = Client::create([
            'nome' => $request->nome_cliente,
            'telefone' => $request->telefone_cliente,
            'endereco' => $request->endereco_cliente,
            'email' => $request->email_cliente,
            'cpf' => $request->cpf_cliente,
        ]);

        // O client_id do cliente recém-criado será automaticamente atribuído
        $cliente_id = $cliente->client_id; 
    } else {
        // Se cliente_id foi enviado, apenas usa o ID informado
        $cliente_id = $request->cliente_id;
    }

    // Criar o pedido
    $pedido = Pedido::create([
        'client_id' => $cliente_id, // Passando o client_id para o pedido
        'data' => $request->data,
        'orcamento' => $request->orcamento,
        'status' => $request->status,
        'prazo' => $request->prazo,
        'data_retirada' => $request->data_retirada,
        'obs' => $request->obs,
    ]);

    // Criar os itens do pedido
    foreach ($request->items as $itemData) {
        Item::create([
            'pedido_id' => $pedido->pedido_id, // Associando o pedido_id
            'nome_item' => $itemData['nome_item'],
            'material' => $itemData['material'],
            'metragem' => $itemData['metragem'],
            'especificacao' => $itemData['especificacao'] ?? null,
        ]);
    }

    // Criar o pagamento do pedido
    Pagamento::create([
        'pedido_id' => $pedido->pedido_id,
        'valor' => $request->valor_pagamento,
        'forma' => $request->forma_pagamento,
    ]);

    return redirect()->route('pedidos.index')->with('success', 'Pedido registrado com sucesso!');
}


}
