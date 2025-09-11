<?php

namespace App\Http\Controllers;

use App\Models\MovimentoEstoque;
use App\Models\Estoque;
use Illuminate\Http\Request;

class MovimentoEstoqueController extends Controller
{
    /**
     * Registra um movimento de estoque (entrada ou saída)
     */
    public function store(Request $request, $estoqueId)
    {
        $estoque = Estoque::findOrFail($estoqueId);

        $data = $request->validate([
            'tipo' => 'required|in:entrada,saida,ajuste',
            'nova_quantidade' => 'required|integer|min:0',
            'descricao' => 'nullable|string|max:255',
        ]);

        $quantidadeAtual = $estoque->quantidadeDisponivel();
        $quantidadeNova = $data['nova_quantidade'];

        // Calcula a diferença
        $diferenca = $quantidadeNova - $quantidadeAtual;

        if($diferenca == 0){
            return redirect()->route('estoque.index')->with('error', 'A quantidade não foi alterada.');
        }

        // Determina automaticamente tipo de movimento
        $tipo = $diferenca > 0 ? 'entrada' : 'saida';

        $estoque->movimentos()->create([
            'quantidade' => abs($diferenca),
            'tipo' => $tipo,
            'obs' => $data['descricao'] ?? null,
            'usuario_id' => auth()->id(),
            'data_movimento' => now(),
        ]);

        return redirect()->route('estoque.index')->with('success', 'Movimento registrado com sucesso!');
    }
}
