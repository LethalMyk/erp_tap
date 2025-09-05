<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Parcela;
use App\Models\ProdutoComprado;

class Despesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'descricao',
        'valor_total',
        'categoria',
        'separador',
        'forma_pagamento',
        'observacao',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Usuário que criou a despesa
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Parcelas vinculadas à despesa
     */
    public function parcelas()
    {
        return $this->hasMany(Parcela::class);
    }

    /**
     * Produtos comprados vinculados à despesa
     */
    public function produtosComprados()
    {
        return $this->hasMany(ProdutoComprado::class, 'despesa_id');
    }

    /**
     * Retorna valor total pago das parcelas
     */
    public function valorPago()
    {
        return $this->parcelas()->where('status', 'PAGO')->sum('valor_parcela');
    }

    /**
     * Retorna valor restante a pagar
     */
    public function valorRestante()
    {
        return $this->valor_total - $this->valorPago();
    }

    /**
     * Retorna status geral da despesa com base nas parcelas
     */
    public function getStatusAttribute()
    {
        $totalParcelas = $this->parcelas()->count();
        if ($totalParcelas == 0) {
            return 'SEM PARCELAS';
        }

        $parcelasPagas = $this->parcelas()->where('status', 'PAGO')->count();

        if ($parcelasPagas == 0) return 'PENDENTE';
        if ($parcelasPagas < $totalParcelas) return 'PARCIAL';
        return 'PAGO';
    }
    
}
