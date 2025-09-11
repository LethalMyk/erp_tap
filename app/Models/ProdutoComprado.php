<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoComprado extends Model
{
    use HasFactory;

    protected $table = 'produtos_comprados';

    protected $fillable = [
        'despesa_id',
        'produto_id',
        'quantidade',
        'unidade_medida',
        'valor_unitario',
        'valor_total',
        'obs',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function despesa()
    {
        return $this->belongsTo(Despesa::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function estoque()
    {
        return $this->hasOne(Estoque::class, 'produto_id', 'produto_id');
    }

    /**
     * Calcula o valor total do produto
     */
    public function calcularValorTotal(): float
    {
        return round($this->quantidade * $this->valor_unitario, 2);
    }
}
