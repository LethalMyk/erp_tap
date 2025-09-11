<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    use HasFactory;

    protected $fillable = [
        'despesa_id',
        'valor_parcela',
        'status',
        'data_vencimento',
        'data_pagamento',
        'numero',
        'forma_pagamento',
        'descricao',
        'chave_pagamento',
        'comprovante',
    ];

    protected static function booted()
    {
        static::creating(function ($parcela) {
            if (!$parcela->descricao && $parcela->despesa) {
                $numero = str_pad($parcela->numero ?? 1, 2, '0', STR_PAD_LEFT);
                $parcela->descricao = $parcela->despesa->descricao . " - $numero";
            }
        });
    }

    public function despesa()
    {
        return $this->belongsTo(Despesa::class);
    }
}
