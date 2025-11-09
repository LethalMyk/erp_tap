<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PedidoService;
use App\Services\ClienteService;
use App\Services\AgendamentoService;
use App\Services\PagamentoService;
use App\Models\Cliente;
use App\Models\PedidoImagem;
use App\Models\Profissional;

class PedidoController extends Controller
{
    protected $pedidoService;
    protected $clienteService;
    protected $agendamentoService;
    protected $pagamentoService;

    public function __construct(
        PedidoService $pedidoService,
        ClienteService $clienteService,
        AgendamentoService $agendamentoService,
        PagamentoService $pagamentoService
    ) {
        $this->pedidoService = $pedidoService;
        $this->clienteService = $clienteService;
        $this->agendamentoService = $agendamentoService;
        $this->pagamentoService = $pagamentoService;
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
        $pedido = $this->pedidoService->criarPedidoCompleto($data);

        return redirect()->route('pedidos.index')
            ->with('success', 'Pedido criado com sucesso, agendamento gerado e pagamentos configurados!');
    }

    public function show($id)
    {
        $pedido = $this->pedidoService->getPedidoCompleto((int)$id);
        return view('pedidos.show', compact('pedido'));
    }

    public function imprimirViaTap($id)
    {
        $pedido = $this->pedidoService->getPedidoCompleto((int)$id);
        return $this->pedidoService->gerarImpressaoViaTap($pedido);
    }

    public function imprimirViaRetirada($id)
    {
        $pedido = $this->pedidoService->getPedidoCompleto((int)$id);
        return $this->pedidoService->gerarImpressaoViaRetirada($pedido);
    }

    public function imprimirViaCompleta($id)
    {
        $pedido = $this->pedidoService->getPedidoCompleto((int)$id);
        return $this->pedidoService->gerarImpressaoViaCompleta($pedido);
    }

    public function adicionarImagem(Request $request, $pedidoId)
    {
        $request->validate([
            'imagens' => 'required',
            'imagens.*' => 'image|max:5120',
        ]);

        $pedido = $this->pedidoService->getPedidoCompleto((int)$pedidoId);
        $this->pedidoService->uploadImagens($pedido, $request->file('imagens'));

        return redirect()->back()->with('success', 'Imagens adicionadas com sucesso!');
    }

    public function removerImagem(PedidoImagem $imagem)
    {
        $this->pedidoService->removerImagem($imagem);
        return redirect()->back()->with('success', 'Imagem removida com sucesso!');
    }

    /**
     * Página Kanban para controle de pedidos
     */
 public function kanban()
{
    // Carrega pedidos com cliente, items, agendamento, imagens e profissional
    $pedidos = \App\Models\Pedido::with([
        'cliente',
        'profissional',
        'items.terceirizadas',
        'imagens',
        'agendamento'
    ])->orderBy('created_at', 'desc')->get();

    $etapas = [
        'Orçamento', 'Agendar', 'Retirar', 'Montado', 'Desmontado',
        'Preparo', 'Corte', 'Costura', 'Montagem', 'Conferencia',
        'Entregar', 'Concluido'
    ];

    return view('pedidos.kanban', compact('pedidos', 'etapas'));
}


    /**
     * Atualiza o status do pedido via AJAX
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required',
            'status' => 'required|string'
        ]);

        // Converter para inteiro para evitar erro de tipo
        $pedidoId = (int) $request->pedido_id;

        $pedido = $this->pedidoService->getPedidoCompleto($pedidoId);
        $pedido->status = $request->status;
        $pedido->save();

        return response()->json(['success' => true]);
    }
}
