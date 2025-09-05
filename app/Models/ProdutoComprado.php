<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Despesa;
use App\Models\Produto;
use App\Models\Estoque;

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

    /**
     * Relaciona ProdutoComprado com a despesa
     */
    public function despesa()
    {
        return $this->belongsTo(Despesa::class, 'despesa_id');
    }

    /**
     * Relaciona ProdutoComprado com o produto
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    /**
     * Retorna o estoque atual do produto comprado
     */
    public function estoque()
    {
        return $this->hasOne(Estoque::class, 'produto_id', 'produto_id');
    }

    /**
     * Calcula o valor total do produto (quantidade * valor_unitario)
     */
    public function calcularValorTotal()
    {
        return round($this->quantidade * $this->valor_unitario, 2);
    }
}
