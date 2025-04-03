<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
    public function index()
    {
        $pagamentos = Pagamento::with('pedido')->get();
        return view('pagamento.index', compact('pagamentos'));
    }

    public function create()
    {
        $pedidos = Pedido::all();
        return view('pagamento.create', compact('pedidos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'valor' => 'required|numeric|min:0',
            'forma' => 'required|in:PIX,DEBITO,DINHEIRO,CREDITO À VISTA,CREDITO PARCELADO,BOLETO,CHEQUE,OUTROS',
            'obs' => 'nullable|string',
        ]);

        // Criar o pagamento
        $pagamento = Pagamento::create($request->all());

        // Atualizar o valor restante no pedido
        $pedido = Pedido::find($request->pedido_id);
        $pedido->valorResta -= $pagamento->valor;

        // Garantir que o valor restante nunca seja negativo
        if ($pedido->valorResta <= 0) {
            $pedido->valorResta = 0;
            $pedido->status = 'PAGO';
        } else {
            $pedido->status = 'RESTA';
        }

        $pedido->save();

        return redirect()->route('pagamento.index')->with('success', 'Pagamento registrado e status do pedido atualizado.');
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

    // Buscar o pedido associado a este pagamento
    $pedido = Pedido::find($pagamento->pedido_id);

    if (!$pedido) {
        return redirect()->route('pagamento.index')->with('error', 'Pedido não encontrado.');
    }

    // Recalcular valor restante
    $totalPagoAntes = Pagamento::where('pedido_id', $pedido->id)->sum('valor');
    $novoTotalPago = ($totalPagoAntes - $pagamento->valor) + $request->valor;
    $novoValorResta = max(0, $pedido->valor - $novoTotalPago);

    // Atualizar o status do pedido
    $status = ($novoValorResta == 0) ? 'PAGO' : 'RESTA';

    // Atualizar pagamento e pedido
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

    // Deletar o pagamento
    $pagamento->delete();

    // Buscar o pedido
    $pedido = Pedido::find($pedidoId);
    if (!$pedido) {
        return redirect()->route('pagamento.index')->with('error', 'Pedido não encontrado.');
    }

    // Recalcular valor restante após a exclusão
    $totalPago = Pagamento::where('pedido_id', $pedidoId)->sum('valor');
    $novoValorResta = max(0, $pedido->valor - $totalPago);

    // Atualizar o status do pedido
    $status = ($novoValorResta == 0) ? 'PAGO' : 'RESTA';

    $pedido->update([
        'valorResta' => $novoValorResta,
        'status' => $status,
    ]);

    return redirect()->route('pagamento.index')->with('success', 'Pagamento excluído com sucesso!');
}

}
