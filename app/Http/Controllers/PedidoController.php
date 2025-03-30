<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Item;
use App\Models\Pagamento;
use App\Models\Client; // Certifique-se de que o modelo Client está importado
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
    // Validação dos dados recebidos
    $request->validate([
        'nome_cliente' => 'nullable|string|max:255',
        'telefone_cliente' => 'nullable|string|max:15',
        'endereco_cliente' => 'nullable|string|max:255',
        'email_cliente' => 'nullable|string|email|max:255',
        'cpf_cliente' => 'nullable|string|max:14',
        'data' => 'required|date',
        'orcamento' => 'required|numeric',
        'status' => 'nullable|string', // Status pode ser opcional aqui
        'prazo' => 'required|date',
        'data_retirada' => 'required|date',
        'obs' => 'nullable|string',
        'items' => 'required|array|min:1|max:10',
        'items.*.nome_item' => 'required|string',
        'items.*.material' => 'required|string',
        'items.*.metragem' => 'required|numeric',
        'items.*.especificacao' => 'nullable|string',
        'pagamentos' => 'required|array|min:1', // Múltiplos pagamentos
        'pagamentos.*.valor' => 'required|numeric',
        'pagamentos.*.forma' => 'required|string',
        'pagamentos.*.descricao' => 'required|string',
    ]);

    // Verificar se o cliente existe ou criar um novo cliente
    $cliente = Client::firstOrCreate(
        ['client_id' => $request->cliente_id],  // Verifica se o cliente existe com o ID
        [
            'nome' => $request->nome_cliente,
            'telefone' => $request->telefone_cliente,
            'endereco' => $request->endereco_cliente,
            'email' => $request->email_cliente,
            'cpf' => $request->cpf_cliente,
        ]
    );

    // Definir o status como "Pendente" se não for fornecido
    $status = $request->status ?? 'Pendente';

    // Criar o pedido com o status padrão "Pendente"
    $pedido = Pedido::create([
        'client_id' => $cliente->client_id, // Passando o client_id para o pedido
        'data' => $request->data,
        'orcamento' => $request->orcamento,
        'status' => $status, // Usando o status informado ou "Pendente"
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

    // Criar os pagamentos do pedido
    foreach ($request->pagamentos as $pagamentoData) {
        Pagamento::create([
            'pedido_id' => $pedido->pedido_id,
            'valor' => $pagamentoData['valor'],
            'forma' => $pagamentoData['forma'],
            'descricao' => $pagamentoData['descricao'],
        ]);
    }

    return redirect()->route('pedidos.index')->with('success', 'Pedido registrado com sucesso!');
}
  public function pesquisar(Request $request)
    {
        $termo = $request->input('termo');  // Pega o termo de pesquisa

        // Realiza a pesquisa no banco de dados
        $pedidos = Pedido::where('pedido_id', 'like', "%{$termo}%")
                         ->orWhereHas('client', function ($query) use ($termo) {
                             $query->where('nome', 'like', "%{$termo}%")
                                   ->orWhere('endereco', 'like', "%{$termo}%")
                                   ->orWhere('cpf', 'like', "%{$termo}%");
                         })
                         ->paginate(10);  // Aqui você pode paginar os resultados

        return view('pedidos.pesquisar', compact('pedidos'));
    }
}
