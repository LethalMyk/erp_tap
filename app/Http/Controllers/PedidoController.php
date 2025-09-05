<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PedidoService;
use App\Services\ClienteService;
use App\Models\Cliente;
use App\Models\PedidoImagem;
use App\Models\Profissional;

class PedidoController extends Controller
{
    protected $pedidoService;
    protected $clienteService;

    public function __construct(PedidoService $pedidoService, ClienteService $clienteService)
    {
        $this->pedidoService = $pedidoService;
        $this->clienteService = $clienteService;
    }

    public function index(Request $request)
    {
        $filters = $request->all();
        $pedidos = $this->pedidoService->listarPedidos($filters);
        return view('pedidos.index', compact('pedidos', 'filters'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        $profissionais = Profissional::orderBy('nome')->get();
        return view('pedidos.create', compact('clientes', 'profissionais'));
    }

    public function store(Request $request)
    {
        $data = $request->all();

        // Cria ou atualiza cliente
        $clienteData = $data['cliente'] ?? [];
        $cliente = $this->clienteService->criarOuAtualizarCliente($clienteData);

        // Prepara dados do pedido completo
        $pedidoData = $data['pedido'] ?? [];
        $pedidoData['cliente_id'] = $cliente->id;
        $pedidoData['qntItens'] = count($data['items'] ?? []);
        $pedidoData['valor'] = $pedidoData['valor'] ?? 0;

        // Prepara array completo para criar pedido com itens, terceirizadas, pagamentos e imagens
        $pedidoCompletoData = [
            'cliente_id' => $cliente->id,
            'cliente' => $clienteData,
            'pedido' => $pedidoData,
            'items' => $data['items'] ?? [],
            'pagamentos' => $data['pagamentos'] ?? [],
            'imagens' => $request->file('imagens') ?? [],
        ];

        // Cria pedido completo
        $pedido = $this->pedidoService->criarPedidoCompleto($pedidoCompletoData);

        return redirect()->route('pedidos.index')
            ->with('success', 'Pedido criado com sucesso!');
    }

    public function imprimirViaTap($id)
{
    $pedido = $this->pedidoService->getPedidoCompleto($id);
    return $this->pedidoService->gerarImpressaoViaTap($pedido);
}

public function imprimirViaRetirada($id)
{
    $pedido = $this->pedidoService->getPedidoCompleto($id);
    return $this->pedidoService->gerarImpressaoViaRetirada($pedido);
}

public function imprimirViaCompleta($id)
{
    $pedido = $this->pedidoService->getPedidoCompleto($id);
    return $this->pedidoService->gerarImpressaoViaCompleta($pedido);
}

    public function show($id)
    {
        $pedido = $this->pedidoService->getPedidoCompleto($id);
        return view('pedidos.show', compact('pedido'));
    }

    public function adicionarImagem(Request $request, Pedido $pedido)
    {
        $request->validate([
            'imagens' => 'required',
            'imagens.*' => 'image|max:5120',
        ]);

        $this->pedidoService->uploadImagens($pedido, $request->file('imagens'));

        return redirect()->back()->with('success', 'Imagens adicionadas com sucesso!');
    }

    public function removerImagem(PedidoImagem $imagem)
    {
        $this->pedidoService->removerImagem($imagem);

        return redirect()->back()->with('success', 'Imagem removida com sucesso!');
    }
}
