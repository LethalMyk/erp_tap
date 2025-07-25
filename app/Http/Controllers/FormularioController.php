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
use App\Models\Agendamento; // no topo do arquivo, se ainda não estiver

     
class FormularioController extends Controller
{
public function store(Request $request)

{
    // Valor total do pedido (já vem do formulário)
    $valorTotal = floatval($request->input('pedido.valor'));
    $pagamentos = $request->input('pagamentos', []);

  // Soma os pagamentos excluindo 'BOLETO', 'CHEQUE' e 'OUTROS'
$totalPago = 0;
foreach ($pagamentos as $pagamento) {
    $forma = $pagamento['forma'] ?? '';
    if (!in_array($forma, ['BOLETO', 'CHEQUE', 'OUTROS'])) {
        $totalPago += floatval($pagamento['valor']);
    }
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
        'andamento' => 'Retirar',  // Definindo o valor padrão 'retirar' para a coluna 'andamento'
    ]);

     // *** Cria o agendamento para a retirada AQUI (fora dos loops) ***
    Agendamento::create([
        'tipo' => 'retirada',
        'data' => $request->input('pedido.data_retirada'),
        'horario' => '08:00', // Pode ajustar para pegar do formulário se quiser
        'nome_cliente' => $cliente->nome,
        'endereco' => $cliente->endereco,
        'telefone' => $cliente->telefone,
        'status' => 'pendente',
        'items' => 'Pedido #' . $pedido->id,
        'obs' => 'Agendamento automático criado ao salvar pedido.',
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
    $forma = $pagamentoData['forma'] ?? '';
$ehEmAberto = in_array($forma, ['BOLETO', 'CHEQUE', 'OUTROS', 'NA ENTREGA', 'A PRAZO']);

    Pagamento::create([
        'pedido_id' => $pedido->id,
        'valor' => $pagamentoData['valor'],
        'forma' => $forma,
        'obs' => $pagamentoData['obs'] ?? null,
        'data' => $ehEmAberto
            ? ($pagamentoData['data'] ?? null) // pega a data se for informada
            : $request->input('pedido.data'), // usa a data do pedido se já for registrado
        'status' => $ehEmAberto ? 'EM ABERTO' : 'PAGAMENTO REGISTRADO',
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

public function adicionarImagem(Request $request, Pedido $pedido)
{
    $request->validate([
        'imagens' => 'required',
        'imagens.*' => 'image|max:5120', // Máximo 5MB por arquivo
    ]);

    foreach ($request->file('imagens') as $imagem) {
        $path = $imagem->store('pedidos', 'public');

        PedidoImagem::create([
            'pedido_id' => $pedido->id,
            'imagem' => $path,
        ]);
    }

    return redirect()->back()->with('success', 'Imagens adicionadas com sucesso!');
}

public function removerImagem(PedidoImagem $imagem)
{
    // Apaga arquivo físico da storage
    \Illuminate\Support\Facades\Storage::disk('public')->delete($imagem->imagem);

    // Apaga registro no banco
    $imagem->delete();

    return redirect()->back()->with('success', 'Imagem removida com sucesso!');
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
