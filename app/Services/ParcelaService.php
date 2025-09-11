<?php

namespace App\Services;

use App\Models\Despesa;
use App\Models\Parcela;

class ParcelaService
{
    /**
     * Cria parcelas de uma despesa
     *
     * @param Despesa $despesa
     * @param array $parcelasData
     * @param array $comprovantes
     */
    public function criarParcelas(Despesa $despesa, array $parcelasData, array $comprovantes = []): void
    {
        if ($despesa->forma_pagamento === 'À VISTA') {
            Parcela::create([
                'despesa_id' => $despesa->id,
                'numero_parcela' => 1,
                'valor_parcela' => $despesa->valor_total,
                'data_vencimento' => $parcelasData['data'] ?? now(),
                'status' => 'PAGO',
                'forma_pagamento' => $parcelasData['forma_pagamento'] ?? 'PIX',
                'chave_pagamento' => $parcelasData['chave_pagamento'][0] ?? null,
                'comprovante' => $comprovantes[0] ?? null,
                'descricao' => $despesa->descricao,
            ]);
        } else {
            $parcelasDesc = $parcelasData['parcelas_descricao'] ?? [$despesa->descricao];
            $parcelasValor = $parcelasData['parcelas_valor'] ?? [$despesa->valor_total];
            $parcelasForma = $parcelasData['parcelas_forma_pagamento'] ?? [];
            $datas = $parcelasData['data_vencimento'] ?? [];
            $chaves = $parcelasData['chave_pagamento'] ?? [];

            foreach ($parcelasDesc as $index => $desc) {
                Parcela::create([
                    'despesa_id' => $despesa->id,
                    'numero_parcela' => $index + 1,
                    'valor_parcela' => $parcelasValor[$index] ?? $despesa->valor_total,
                    'data_vencimento' => $datas[$index] ?? null,
                    'status' => 'PENDENTE',
                    'forma_pagamento' => $parcelasForma[$index] ?? 'PIX',
                    'chave_pagamento' => $chaves[$index] ?? null,
                    'comprovante' => $comprovantes[$index] ?? null,
                    'descricao' => $desc,
                ]);
            }
        }
    }

    /**
     * Registrar pagamento de parcela
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
