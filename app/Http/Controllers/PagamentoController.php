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
        // Buscar todos os pedidos com dados relacionados (cliente e pagamentos)
        $pedidos = Pedido::with(['cliente', 'pagamentos'])
            ->when($request->nome, fn($q) => $q->whereHas('cliente', fn($sub) =>
                $sub->where('nome', 'like', '%' . $request->nome . '%')))
            ->when($request->endereco, fn($q) => $q->whereHas('cliente', fn($sub) =>
                $sub->where('endereco', 'like', '%' . $request->endereco . '%')))
            ->orderByDesc('created_at')
            ->get();

        // Preparar estrutura final com pagamentos filtrados e totais
        $resultado = $pedidos->map(function ($pedido) use ($request) {
            // Filtrar pagamentos individualmente conforme filtros do request
            $pagamentosFiltrados = $pedido->pagamentos->filter(function ($p) use ($request) {
                return (!$request->forma || $p->forma === $request->forma)
                    && (!$request->status || $p->status === $request->status)
                    && (!$request->data_inicio || Carbon::parse($p->data)->gte($request->data_inicio))
                    && (!$request->data_fim || Carbon::parse($p->data)->lte($request->data_fim));
            });

            // Somar apenas pagamentos filtrados e com status PAGAMENTO REGISTRADO
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
            'statusFilter' => $request->status,
            'formaFilter' => $request->forma,
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
