<?php

namespace App\Services;

use App\Models\MovimentoEstoque;
use App\Models\Estoque;
use Illuminate\Support\Facades\Auth;

class MovimentoEstoqueService
{
    /**
     * Registra um movimento de estoque (entrada ou saída)
     */
    public function registrarMovimento(Estoque $estoque, float $quantidade, string $vinculo = null, string $obs = null)
    {
        if ($quantidade == 0) return null;

        // Atualiza a quantidade disponível do estoque
        $estoque->quantidade_disponivel = ($estoque->quantidade_disponivel ?? 0) + $quantidade;
        $estoque->save();

        // Define tipo automaticamente
        $tipo = $quantidade > 0 ? 'entrada' : 'saida';

        // Cria o registro de movimento
        return MovimentoEstoque::create([
            'estoque_id' => $estoque->id,
            'quantidade' => abs($quantidade),
            'tipo' => $tipo,
            'vinculo' => $vinculo,
            'usuario_id' => Auth::id(),
            'data_movimento' => now(),
            'obs' => $obs,
        ]);
    }
}
