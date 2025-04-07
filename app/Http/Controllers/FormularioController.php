<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Item;
use App\Models\Pagamento;
use App\Models\PedidoImagem;
use App\Models\Terceirizada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Servico;
use App\Models\Profissional;

class FormularioController extends Controller
{
public function store(Request $request)

{
    // Valor total do pedido (já vem do formulário)
    $valorTotal = floatval($request->input('pedido.valor'));
    $pagamentos = $request->input('pagamentos', []);

    // Soma os pagamentos
    $totalPago = 0;
    foreach ($pagamentos as $pagamento) {
        $totalPago += floatval($pagamento['valor']);
    }

    // Define status
    $status = $totalPago >= $valorTotal ? 'PAGO' : 'RESTA';

    // 1️⃣ Criar Cliente
    $cliente = Cliente::create([
        'nome' => $request->input('cliente.nome'),
        'telefone' => $request->input('cliente.telefone'),
        'endereco' => $request->input('cliente.endereco'),
        'cpf' => $request->input('cliente.cpf'),
        'email' => $request->input('cliente.email'),
    ]);

    // 2️⃣ Criar Pedido
    $qntItens = count($request->input('items', []));

    $pedido = Pedido::create([
        'cliente_id' => $cliente->id,
        'data' => $request->input('pedido.data'),
        'data_retirada' => $request->input('pedido.data_retirada'),
        'prazo' => $request->input('pedido.prazo'),
        'valor' => $valorTotal,
        'valorResta' => $valorTotal - $totalPago,
        'qntItens' => $qntItens,
        'status' => $status, // ⬅️ aqui adiciona o status calculado
    ]);

    // 3️⃣ Criar Itens e Serviços Terceirizados
    foreach ($request->input('items', []) as $itemData) {
        $item = Item::create([
            'pedido_id' => $pedido->id,
            'nomeItem' => $itemData['nomeItem'],
            'material' => $itemData['material'],
            'metragem' => $itemData['metragem'],
            'especifi' => $itemData['especifi'] ?? null,
            'material_disponib' => $itemData['material_disponib'] ?? 'Pedir',
        ]);

        // Serviços Terceirizados
        if (!empty($itemData['terceirizadas'])) {
            foreach ($itemData['terceirizadas'] as $terceirizadaData) {
                Terceirizada::create([
                    'item_id' => $item->id,
                    'pedido_id' => $pedido->id,
                    'tipo' => $terceirizadaData['tipo'],
                    'obs' => $terceirizadaData['obs'] ?? null,
                    'statusPg' => 'Pendente',
                ]);
            }
        }
    }

    // 4️⃣ Criar Pagamentos
    foreach ($pagamentos as $pagamentoData) {
        Pagamento::create([
            'pedido_id' => $pedido->id,
            'valor' => $pagamentoData['valor'],
            'forma' => $pagamentoData['forma'],
            'obs' => $pagamentoData['obs'],
        ]);
    }

    // 5️⃣ Upload de Imagens
    if ($request->hasFile('imagens')) {
        foreach ($request->file('imagens') as $imagem) {
            if ($imagem->isValid()) {
                $path = $imagem->store('pedidos', 'public');
                PedidoImagem::create([
                    'pedido_id' => $pedido->id,
                    'imagem' => $path
                ]);
            }
        }
    }

    // Criar o serviço associado
    $servico = new Servico();
    $servico->pedido_id = $pedido->id;
    $servico->codigo_servico = 'O.S' . $pedido->id;
$tapeceiroId = $request->input('pedido.tapeceiro'); // Aqui define a variável

$servico->profissional_id = $tapeceiroId ?: null; // Se não veio nada, salva como null
    
    // ... outros campos do serviço que você quiser preencher
    $servico->save();

    return redirect()->route('formulario.index')->with('success', 'Formulário salvo com sucesso!');
}

    public function index()
    {
$profissionais = Profissional::orderBy('nome')->get();
    return view('formulario', compact('profissionais'));
    }
    public function visualizar($id)
{
    $pedido = Pedido::with([
        'cliente',
        'items.terceirizadas',
        'pagamentos',
        'imagens'
    ])->findOrFail($id);

    return view('viewpedido', compact('pedido'));
}

}
