<?php

namespace App\Services;

use App\Models\Despesa;
use App\Models\Parcela;
use Illuminate\Support\Carbon;

class ParcelaService
{
    /**
     * Cria múltiplas parcelas de uma despesa.
     *
     * @param Despesa $despesa
     * @param array $parcelasData
     * @param array $comprovantes
     */
    public function criarParcelas(Despesa $despesa, array $parcelasData, array $comprovantes = []): void
    {
        // Despesa à vista: cria uma única parcela
        if ($despesa->forma_pagamento === 'À VISTA') {
            Parcela::create([
                'despesa_id' => $despesa->id,
                'numero_parcela' => 1,
                'valor_parcela' => $despesa->valor_total,
                'data_vencimento' => isset($parcelasData['data']) ? Carbon::parse($parcelasData['data']) : now(),
                'status' => 'PAGO',
                'forma_pagamento' => $parcelasData['forma_pagamento'] ?? 'PIX',
                'chave_pagamento' => $parcelasData['chave_pagamento'][0] ?? null,
                'comprovante' => $comprovantes[0] ?? null,
                'descricao' => $despesa->descricao,
            ]);
            return;
        }

        // Despesa a prazo: cria múltiplas parcelas
        $parcelasValor = $parcelasData['parcelas_valor'] ?? [];
        $parcelasDescricao = $parcelasData['parcelas_descricao'] ?? [];
        $parcelasForma = $parcelasData['parcelas_forma_pagamento'] ?? [];
        $parcelasVencimento = $parcelasData['data_vencimento'] ?? [];
        $parcelasChave = $parcelasData['chave_pagamento'] ?? [];

        $numParcelas = max(count($parcelasValor), count($parcelasDescricao));

        for ($i = 0; $i < $numParcelas; $i++) {
            $valor = $parcelasValor[$i] ?? ($despesa->valor_total / $numParcelas);
            $desc = $parcelasDescricao[$i] ?? $despesa->descricao . ' - ' . ($i + 1);
            $forma = $parcelasForma[$i] ?? 'PIX';

            // Parsing correto da data
            if (isset($parcelasVencimento[$i])) {
                $vencimento = Carbon::parse($parcelasVencimento[$i]);
            } else {
                // Se não passar, calcula um vencimento mensal automaticamente
                $vencimento = Carbon::parse($despesa->data ?? now())->addMonth($i);
            }

            Parcela::create([
                'despesa_id' => $despesa->id,
                'numero_parcela' => $i + 1,
                'valor_parcela' => $valor,
                'data_vencimento' => $vencimento,
                'status' => 'PENDENTE',
                'forma_pagamento' => $forma,
                'chave_pagamento' => $parcelasChave[$i] ?? null,
                'comprovante' => $comprovantes[$i] ?? null,
                'descricao' => $desc,
            ]);
        }
    }

    /**
     * Registrar pagamento de uma parcela específica.
     *
     * @param Parcela $parcela
     * @param array $dados
     * @return Parcela
     */
    public function registrarPagamento(Parcela $parcela, array $dados): Parcela
    {
        $parcela->data_pagamento = $dados['data_pagamento'] ?? now();
        $parcela->descricao = $dados['descricao'] ?? $parcela->descricao;
        $parcela->status = 'PAGO';

        if (!empty($dados['comprovante'])) {
            $parcela->comprovante = $dados['comprovante'];
        }

        $parcela->save();

        return $parcela;
    }
}
