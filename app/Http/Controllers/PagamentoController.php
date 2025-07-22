<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PagamentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pedido::with(['cliente', 'pagamentos']);

        // Filtrar por ID
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }

        // Filtrar por nome do cliente
        if ($request->filled('nome')) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->nome . '%');
            });
        }

        // Filtrar por endereço do cliente
        if ($request->filled('endereco')) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where('endereco', 'like', '%' . $request->endereco . '%');
            });
        }

        // Filtrar por telefone do cliente
        if ($request->filled('telefone')) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where('telefone', 'like', '%' . $request->telefone . '%');
            });
        }

        // Filtrar por status do pedido
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtrar por forma de pagamento (múltiplas formas)
        if ($request->filled('forma')) {
            $formas = is_array($request->forma) ? $request->forma : [$request->forma];
            $query->whereHas('pagamentos', function ($q) use ($formas) {
                $q->whereIn('forma', $formas);
            });
        }
// Filtro por anos (array)
if ($request->filled('ano')) {
    $anos = $request->input('ano');
    $query->whereIn(\DB::raw('YEAR(created_at)'), $anos);
}

// Filtro por meses (array)
if ($request->filled('mes')) {
    $meses = $request->input('mes');
    $query->whereIn(\DB::raw('MONTH(created_at)'), $meses);
}

        $pedidos = $query->orderByDesc('created_at')->get();

        $resultado = $pedidos->map(function ($pedido) {
            $pagamentosFiltrados = $pedido->pagamentos;

            $totalPago = $pagamentosFiltrados->where('status', 'PAGAMENTO REGISTRADO')->sum('valor');
            $valorResta = max(0, $pedido->valor - $totalPago);

            return [
                'pedido' => $pedido,
                'pagamentos' => $pagamentosFiltrados,
                'total_pago' => $totalPago,
                'valor_resta' => $valorResta,
            ];
        });

        return view('pagamento.index', [
            'pedidos' => $resultado,
            'filters' => $request->only(['id', 'nome', 'endereco', 'telefone', 'status', 'forma']),
        ]);
    }

    public function create($cliente_id = null)
    {
        if ($cliente_id) {
            $pedidos = Pedido::where('cliente_id', $cliente_id)->get();
        } else {
            $pedidos = Pedido::all();
        }
        return view('pagamento.create', compact('pedidos', 'cliente_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'valor' => 'required|numeric|min:0',
            'forma' => 'required|in:PIX,DEBITO,DINHEIRO,CREDITO À VISTA,CREDITO PARCELADO,BOLETO,CHEQUE,OUTROS,A PRAZO,NA ENTREGA',
            'obs' => 'nullable|string',
            'data' => 'nullable|date',
        ]);

        $pedido = Pedido::findOrFail($request->pedido_id);

        $forma = $request->forma;
        $formasEmAberto = ['BOLETO', 'CHEQUE', 'OUTROS', 'NA ENTREGA', 'A PRAZO'];
        $status = in_array($forma, $formasEmAberto) ? 'EM ABERTO' : 'PAGAMENTO REGISTRADO';
        $data = $request->input('data') ?? now();

        $pagamento = Pagamento::create([
            'pedido_id' => $pedido->id,
            'valor' => $request->valor,
            'forma' => $forma,
            'obs' => $request->obs,
            'status' => $status,
            'data' => $data,
        ]);

        $totalPago = Pagamento::where('pedido_id', $pedido->id)
            ->where('status', 'PAGAMENTO REGISTRADO')
            ->sum('valor');

        $novoValorResta = max(0, $pedido->valor - $totalPago);
        $novoStatusPedido = ($novoValorResta == 0) ? 'PAGO' : 'RESTA';

        $pedido->update([
            'valorResta' => $novoValorResta,
            'status' => $novoStatusPedido,
        ]);

        return redirect()->route('pagamento.index')->with('success', 'Pagamento registrado com sucesso.');
    }

    public function registrar(Request $request, $id)
    {
        $pagamento = Pagamento::findOrFail($id);

        if ($pagamento->status === 'EM ABERTO') {
            $pagamento->status = 'PAGAMENTO REGISTRADO';
            $pagamento->data_registro = now();
            $pagamento->obs = $request->input('obs'); // nova observação
            $pagamento->save();

            $totalPago = Pagamento::where('pedido_id', $pagamento->pedido_id)
                ->where('status', '!=', 'EM ABERTO')
                ->sum('valor');

            $pedido = $pagamento->pedido;
            $novoValorResta = max(0, $pedido->valor - $totalPago);
            $novoStatusPedido = ($novoValorResta == 0) ? 'PAGO' : 'RESTA';

            $pedido->update([
                'valorResta' => $novoValorResta,
                'status' => $novoStatusPedido,
            ]);

            return redirect()->back()->with('success', 'Pagamento registrado com sucesso!');
        }

        return redirect()->back()->with('info', 'Este pagamento já está registrado.');
    }

    public function show(Pagamento $pagamento)
    {
        return view('pagamento.show', compact('pagamento'));
    }

    public function edit(Pagamento $pagamento)
    {
        $pedidos = Pedido::all();
        return view('pagamento.edit', compact('pagamento', 'pedidos'));
    }

    public function update(Request $request, Pagamento $pagamento)
    {
        $request->validate([
            'valor' => 'required|numeric|min:0',
            'forma' => 'required|in:PIX,DEBITO,DINHEIRO,CREDITO À VISTA,CREDITO PARCELADO,BOLETO,CHEQUE,OUTROS',
            'obs' => 'nullable|string',
        ]);

        $pedido = Pedido::find($pagamento->pedido_id);

        if (!$pedido) {
            return redirect()->route('pagamento.index')->with('error', 'Pedido não encontrado.');
        }

        $totalPagoAntes = Pagamento::where('pedido_id', $pedido->id)->sum('valor');
        $novoTotalPago = ($totalPagoAntes - $pagamento->valor) + $request->valor;
        $novoValorResta = max(0, $pedido->valor - $novoTotalPago);

        $status = ($novoValorResta == 0) ? 'PAGO' : 'RESTA';

        $pagamento->update([
            'valor' => $request->valor,
            'forma' => $request->forma,
            'obs' => $request->obs,
        ]);

        $pedido->update([
            'valorResta' => $novoValorResta,
            'status' => $status,
        ]);

        return redirect()->route('pagamento.index')->with('success', 'Pagamento atualizado com sucesso!');
    }

    public function destroy(Pagamento $pagamento)
    {
        $pedidoId = $pagamento->pedido_id;

        $pagamento->delete();

        $pedido = Pedido::find($pedidoId);
        if (!$pedido) {
            return redirect()->route('pagamento.index')->with('error', 'Pedido não encontrado.');
        }

        $totalPago = Pagamento::where('pedido_id', $pedidoId)->sum('valor');
        $novoValorResta = max(0, $pedido->valor - $totalPago);

        $status = ($novoValorResta == 0) ? 'PAGO' : 'RESTA';

        $pedido->update([
            'valorResta' => $novoValorResta,
            'status' => $status,
        ]);

        return redirect()->route('pagamento.index')->with('success', 'Pagamento excluído com sucesso!');
    }
}
