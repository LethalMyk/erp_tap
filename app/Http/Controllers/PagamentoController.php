<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
 public function index(Request $request)
{
    $query = Pagamento::with(['pedido.cliente'])
        ->when($request->status, function ($query) use ($request) {
            $query->where('status', $request->status);
        })
        ->when($request->forma, function ($query) use ($request) {
            $query->where('forma', $request->forma);
        })
        ->when($request->nome, function ($query) use ($request) {
            $query->whereHas('pedido.cliente', function ($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->nome . '%');
            });
        })
        ->when($request->endereco, function ($query) use ($request) {
            $query->whereHas('pedido.cliente', function ($q) use ($request) {
                $q->where('endereco', 'like', '%' . $request->endereco . '%');
            });
        })
        ->when($request->data_inicio, function ($query) use ($request) {
            $query->whereDate('data', '>=', $request->data_inicio);
        })
        ->when($request->data_fim, function ($query) use ($request) {
            $query->whereDate('data', '<=', $request->data_fim);
        })
        ->when($request->cliente_id, function ($query) use ($request) {
            $query->whereHas('pedido', function ($q) use ($request) {
                $q->where('cliente_id', $request->cliente_id);
            });
        })
        ->orderByDesc('created_at');

    // Usar paginate para não carregar tudo
    $pagamentos = $query->paginate(20);

    // Calcular valor resta para cada pagamento da página atual
    foreach ($pagamentos as $pagamento) {
        $pedido = $pagamento->pedido;

        // Somar pagamentos registrados para o pedido
        $totalPago = Pagamento::where('pedido_id', $pedido->id)
            ->where('status', 'PAGAMENTO REGISTRADO')
            ->sum('valor');

        $pagamento->valor_resta_pedido = max($pedido->valor - $totalPago, 0);
    }

    return view('pagamento.index', compact('pagamentos'))
        ->with([
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
    'forma' => 'required|in:PIX,DEBITO,DINHEIRO,CREDITO À VISTA,CREDITO PARCELADO,BOLETO,CHEQUE,OUTROS',
    'obs' => 'nullable|string',
    'data' => 'nullable|date',  // aqui
]);


    $pedido = Pedido::findOrFail($request->pedido_id);

    // Regras de status e data
    $forma = $request->forma;
    $ehEmAberto = in_array($forma, ['BOLETO', 'CHEQUE', 'OUTROS']);
    $status = $ehEmAberto ? 'EM ABERTO' : 'PAGAMENTO REGISTRADO';
$data = $ehEmAberto ? ($request->input('data') ?? null) : $pedido->data;

    // Criar pagamento
    $pagamento = Pagamento::create([
        'pedido_id' => $pedido->id,
        'valor' => $request->valor,
        'forma' => $forma,
        'obs' => $request->obs,
        'status' => $status,
        'data' => $data,
    ]);

    // Atualizar valor restante e status do pedido
    // Atualizar valor restante e status do pedido
$totalPago = Pagamento::where('pedido_id', $pedido->id)
    ->whereNotIn('forma', ['BOLETO', 'CHEQUE', 'OUTROS'])
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

        // Soma somente pagamentos registrados para atualizar valor restante e status
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
