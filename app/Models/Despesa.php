<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Despesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'descricao',
        'valor_total',
        'categoria',
        'forma_pagamento',
        'observacao',
        'created_by',
    ];

    /**
     * Boot method para gerar o despesa_id automaticamente.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->despesa_id)) {
                $model->despesa_id = 'D-' . time() . '-' . rand(100, 999); // ex: D-1694440000-123
            }
        });
    }

    /**
     * Relacionamento: uma despesa possui várias parcelas.
     */
    public function parcelas()
    {
        return $this->hasMany(Parcela::class);
    }

    /**
     * Relacionamento: uma despesa possui vários produtos comprados.
     */
    public function produtosComprados()
    {
        return $this->hasMany(ProdutoComprado::class);
    }

    /**
     * Relacionamento: uma despesa possui várias imagens/comprovantes.
     */
    public function imagens()
    {
        return $this->hasMany(DespesaImagem::class);
    }

    /**
     * Cria automaticamente parcelas de acordo com a forma de pagamento.
     */
    public function criarParcelas(array $dados, ?array $comprovantes = null)
    {
        if ($this->forma_pagamento === 'À VISTA') {
            $this->parcelas()->create([
                'numero' => 1,
                'valor_parcela' => $this->valor_total,
                'status' => 'PAGO',
                'data_vencimento' => $dados['data'] ?? now(),
                'forma_pagamento' => $dados['forma_pagamento_avista'] ?? 'PIX',
                'chave_pagamento' => $dados['chave_pagamento'][0] ?? null,
                'comprovante' => $comprovantes[0] ?? null,
            ]);
        } else {
            $parcelasDesc = $dados['parcelas_descricao'] ?? [$this->descricao];
            $parcelasValor = $dados['parcelas_valor'] ?? [$this->valor_total];
            $parcelasForma = $dados['parcelas_forma_pagamento'] ?? [];
            $datas = $dados['data_vencimento'] ?? [null];
            $chaves = $dados['chave_pagamento'] ?? [];

            foreach ($parcelasDesc as $index => $desc) {
                $this->parcelas()->create([
                    'numero' => $index + 1,
                    'descricao' => $desc,
                    'valor_parcela' => $parcelasValor[$index] ?? $this->valor_total,
                    'status' => 'PENDENTE',
                    'data_vencimento' => $datas[$index] ?? null,
                    'forma_pagamento' => $parcelasForma[$index] ?? 'PIX',
                    'chave_pagamento' => $chaves[$index] ?? null,
                    'comprovante' => $comprovantes[$index] ?? null,
                ]);
            }
        }
    }
}
