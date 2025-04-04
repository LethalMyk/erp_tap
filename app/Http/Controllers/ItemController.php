<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Item;
use App\Models\Pagamento;
use Illuminate\Http\Request;

class FormularioController extends Controller
{
    public function index()
    {
        return view('formulario');
    }

    public function store(Request $request)
    {
        // ✅ Validação dos dados
        $request->validate([
            'cliente.nome' => 'required|string',
            'cliente.telefone' => 'required|string',
            'cliente.endereco' => 'required|string',
            'cliente.cpf' => 'required|string',
            'cliente.email' => 'required|email',
            'pedido.data' => 'required|date',
            'pedido.data_retirada' => 'nullable|date',
            'pedido.prazo' => 'nullable|date',
            'pedido.valor' => 'required|numeric',
            'pedido.valor_resta' => 'required|numeric',
            'items' => 'required|array',
            'items.*.nomeItem' => 'required|string',
            'items.*.material' => 'required|string',
            'items.*.metragem' => 'required|numeric',
            'items.*.especifi' => 'nullable|string',
            'pagamentos' => 'nullable|array',
            'pagamentos.*.valor' => 'required|numeric',
            'pagamentos.*.forma' => 'required|string',
            'pagamentos.*.obs' => 'nullable|string',
        ]);

        // 1️⃣ Criar Cliente
        $cliente = Cliente::create([
            'nome' => $request->input('cliente.nome'),
            'telefone' => $request->input('cliente.telefone'),
            'endereco' => $request->input('cliente.endereco'),
            'cpf' => $request->input('cliente.cpf'),
            'email' => $request->input('cliente.email'),
        ]);

        // 2️⃣ Criar Pedido
        $pedido = Pedido::create([
            'cliente_id' => $cliente->id,
            'data' => $request->input('pedido.data'),
            'data_retirada' => $request->input('pedido.data_retirada'),
            'prazo' => $request->input('pedido.prazo'),
            'valor' => $request->input('pedido.valor'),
            'valor_resta' => $request->input('pedido.valor_resta'),
        ]);

        // 3️⃣ Criar Itens (com validação)
            Item::create([
                'pedido_id' => $pedido->id,
                'nomeItem' => $itemData['nomeItem'],
                'material' => $itemData['material'],
                'metragem' => $itemData['metragem'],
                'especifi' => $itemData['especifi'] ?? null,
            ]);

        // 4️⃣ Criar Pagamentos
        foreach ($request->input('pagamentos', []) as $pagamentoData) {
            Pagamento::create([
                'pedido_id' => $pedido->id,
                'valor' => $pagamentoData['valor'],
                'forma' => $pagamentoData['forma'],
                'obs' => $pagamentoData['obs'] ?? null,
            ]);
        }

        return redirect()->route('formulario.index')->with('success', 'Formulário salvo com sucesso!');
    }
}
