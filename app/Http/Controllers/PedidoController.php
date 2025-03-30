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
// Atualize o método store para garantir que cada imagem seja salva corretamente e associada ao pedido

public function store(Request $request)
{
    // Validação
    $request->validate([
        'nome_cliente' => 'nullable|string|max:255',
        'telefone_cliente' => 'nullable|string|max:15',
        'endereco_cliente' => 'nullable|string|max:255',
        'email_cliente' => 'nullable|string|email|max:255',
        'cpf_cliente' => 'nullable|string|max:14',
        'data' => 'required|date',
        'orcamento' => 'required|numeric',
        'status' => 'nullable|string',
        'prazo' => 'required|date',
        'data_retirada' => 'required|date',
        'obs' => 'nullable|string',
        'items' => 'required|array|min:1|max:10',
        'items.*.nome_item' => 'required|string',
        'items.*.material' => 'required|string',
        'items.*.metragem' => 'required|numeric',
        'items.*.especificacao' => 'nullable|string',
        'pagamentos' => 'required|array|min:1',
        'pagamentos.*.valor' => 'required|numeric',
        'pagamentos.*.forma' => 'required|string',
        'pagamentos.*.descricao' => 'required|string',
        'imagens' => 'nullable|array',
        'imagens.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // Verificar se o cliente existe ou criar um novo
    $cliente = Client::firstOrCreate(
        ['client_id' => $request->cliente_id],
        [
            'nome' => $request->nome_cliente,
            'telefone' => $request->telefone_cliente,
            'endereco' => $request->endereco_cliente,
            'email' => $request->email_cliente,
            'cpf' => $request->cpf_cliente,
        ]
    );

    // Criar o pedido
    $pedido = Pedido::create([
        'client_id' => $cliente->client_id,
        'data' => $request->data,
        'orcamento' => $request->orcamento,
        'status' => $request->status ?? 'Pendente',
        'prazo' => $request->prazo,
        'data_retirada' => $request->data_retirada,
        'obs' => $request->obs,
    ]);

    // Criar os itens
    foreach ($request->items as $itemData) {
        Item::create([
            'pedido_id' => $pedido->pedido_id,
            'nome_item' => $itemData['nome_item'],
            'material' => $itemData['material'],
            'metragem' => $itemData['metragem'],
            'especificacao' => $itemData['especificacao'] ?? null,
        ]);
    }

    // Criar os pagamentos
    foreach ($request->pagamentos as $pagamentoData) {
        Pagamento::create([
            'pedido_id' => $pedido->pedido_id,
            'valor' => $pagamentoData['valor'],
            'forma' => $pagamentoData['forma'],
            'descricao' => $pagamentoData['descricao'],
        ]);
    }

    // Armazenamento das imagens
    if ($request->hasFile('imagens')) {
        foreach ($request->file('imagens') as $imagem) {
            $path = $imagem->store('pedidos', 'public');
            $pedido->imagens()->create([
                'imagem_path' => $path
            ]);
        }
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

    public function detalhes($pedido_id)
{
    // Busca o pedido com as relações necessárias
    $pedido = Pedido::with(['client', 'itens', 'imagens']) // Carrega as relações de cliente, itens e imagens
        ->where('pedido_id', $pedido_id)
        ->firstOrFail();  // Se não encontrar o pedido, retorna um erro 404

    return view('pedidos.detalhes', compact('pedido'));  // Retorna a visão com os dados do pedido
}

}