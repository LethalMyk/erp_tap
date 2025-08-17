<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Despesa;
use App\Models\Produto;

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
     * Relaciona com a despesa
     */
    public function despesa()
    {
        return $this->belongsTo(Despesa::class, 'despesa_id');
    }

    /**
     * Relaciona com o produto
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
