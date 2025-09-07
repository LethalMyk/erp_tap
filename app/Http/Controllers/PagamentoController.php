<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PagamentoService;
use App\Models\Pedido;
use App\Models\Pagamento;

class PagamentoController extends Controller
{
    protected $service;

    public function __construct(PagamentoService $service)
    {
        $this->service = $service;
    }

    /**
     * Lista todos os pedidos com pagamentos e valores calculados
     * Filtra por cliente se passar cliente_id na query string
     */
    public function index(Request $request)
    {
        $clienteId = $request->query('cliente_id');

        $pedidosQuery = Pedido::with(['cliente', 'pagamentos']);
        if ($clienteId) {
            $pedidosQuery->where('cliente_id', $clienteId);
        }

        $pedidos = $pedidosQuery->get()->map(function ($pedido) {
            $totalPago = $pedido->pagamentos
                ->where('status', 'PAGAMENTO REGISTRADO')
                ->sum('valor');

            $valorResta = max(0, $pedido->valor - $totalPago);

            return [
                'pedido' => $pedido,
                'pagamentos' => $pedido->pagamentos,
                'total_pago' => $totalPago,
                'valor_resta' => $valorResta,
            ];
        });

        return view('pagamento.index', compact('pedidos', 'clienteId'));
    }

    /**
     * Formulário de criação de pagamento
     */
    public function create(Request $request)
    {
        $clienteId = $request->query('cliente_id');

        if ($clienteId) {
            $pedidos = Pedido::where('cliente_id', $clienteId)->get();
        } else {
            $pedidos = Pedido::all();
        }

        return view('pagamento.create', compact('pedidos', 'clienteId'));
    }

    /**
     * Armazena um novo pagamento
     */
    public function store(Request $request)
    {
        $dados = $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'valor' => 'required|numeric|min:0',
            'forma' => 'required|string',
            'obs' => 'nullable|string',
            'data' => 'nullable|date',
        ]);

        $this->service->criar($dados);

        return redirect()->route('pagamento.index', ['cliente_id' => $request->input('cliente_id')])
                         ->with('success', 'Pagamento registrado com sucesso.');
    }

    /**
     * Registra manualmente um pagamento que estava em aberto
     */
    public function registrar(Request $request, $id)
    {
        $pagamento = Pagamento::findOrFail($id);

        $this->service->registrar($pagamento, $request->input('obs'));

        return redirect()->back()
                         ->with('success', 'Pagamento registrado com sucesso!');
    }

    public function edit(Pagamento $pagamento)
    {
        $pedidos = Pedido::all();
        return view('pagamento.edit', compact('pagamento', 'pedidos'));
    }

    public function update(Request $request, Pagamento $pagamento)
    {
        $dados = $request->validate([
            'valor' => 'required|numeric|min:0',
            'forma' => 'required|string',
            'obs' => 'nullable|string',
        ]);

        $this->service->atualizar($pagamento, $dados);

        return redirect()->route('pagamento.index', ['cliente_id' => $pagamento->pedido->cliente_id])
                         ->with('success', 'Pagamento atualizado com sucesso!');
    }

    public function destroy(Pagamento $pagamento)
    {
        $clienteId = $pagamento->pedido->cliente_id;

        $this->service->deletar($pagamento);

        return redirect()->route('pagamento.index', ['cliente_id' => $clienteId])
                         ->with('success', 'Pagamento excluído com sucesso!');
    }
}
