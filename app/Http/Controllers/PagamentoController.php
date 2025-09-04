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

  public function index(Request $request)
{
    $pedidos = Pedido::with(['cliente', 'pagamentos'])->get();

    $pedidos = $pedidos->map(function ($pedido) {
        $totalPago = $pedido->pagamentos->where('status', 'PAGAMENTO REGISTRADO')->sum('valor');
        $valorResta = max(0, $pedido->valor - $totalPago);
        return [
            'pedido' => $pedido,
            'pagamentos' => $pedido->pagamentos,
            'total_pago' => $totalPago,
            'valor_resta' => $valorResta,
        ];
    });

    return view('pagamento.index', compact('pedidos'));
}


    public function create($cliente_id = null)
    {
        $pedidos = $cliente_id ? Pedido::where('cliente_id', $cliente_id)->get() : Pedido::all();
        return view('pagamento.create', compact('pedidos', 'cliente_id'));
    }

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

        return redirect()->route('pagamento.index')->with('success', 'Pagamento registrado com sucesso.');
    }

    public function registrar(Request $request, $id)
    {
        $pagamento = Pagamento::findOrFail($id);
        $this->service->registrar($pagamento, $request->input('obs'));
        return redirect()->back()->with('success', 'Pagamento registrado com sucesso!');
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

        return redirect()->route('pagamento.index')->with('success', 'Pagamento atualizado com sucesso!');
    }

    public function destroy(Pagamento $pagamento)
    {
        $this->service->deletar($pagamento);
        return redirect()->route('pagamento.index')->with('success', 'Pagamento excluído com sucesso!');
    }
}
