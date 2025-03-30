<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Pagamento;
use Illuminate\Http\Request;

class PagamentoController extends Controller
{
    // Exibe o formulário de pagamento
    public function showForm($pedido_id)
    {
        $pedido = Pedido::findOrFail($pedido_id);
        // Carrega os pagamentos já realizados para o pedido
        $pagamentos = $pedido->pagamentos;

        return view('pagamento.form', compact('pedido', 'pagamentos'));
    }

    // Processa o pagamento
    public function store(Request $request, $pedido_id)
    {
        $pedido = Pedido::findOrFail($pedido_id);

        // Valida o valor do pagamento
        $request->validate([
            'valor' => 'required|numeric|min:0.01',
            'forma' => 'required|string',
            'descricao' => 'nullable|string',
        ]);

        // Cria o pagamento
        $pagamento = new Pagamento([
            'pedido_id' => $pedido_id,
            'valor' => number_format($request->valor, 2, '.', ''), // Formatar o valor para 2 casas decimais
            'forma' => $request->forma,
            'descricao' => $request->descricao,
        ]);

        $pagamento->save();

        // Verificar se o total de pagamentos é igual ou maior que o orçamento
        $totalPago = $pedido->pagamentos->sum('valor'); // Soma dos valores de todos os pagamentos
        $valorTotal = $pedido->orcamento;  // Verifique se o valor correto do pedido está sendo usado

        if ($totalPago >= $valorTotal) {
            $pedido->status = 'Pago';  // Atualiza o status para "Pago"
        } else {
            $pedido->status = 'Parcialmente Pago'; // Caso contrário, marca como "Parcialmente Pago"
        }

        // Atualiza o status do pedido
        $pedido->save();

        // Redireciona de volta para a página de pagamento
        return redirect()->route('pagamento.form', $pedido_id)->with('success', 'Pagamento realizado com sucesso!');
    }
}
