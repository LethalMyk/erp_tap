<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaCompra extends Model
{
    use HasFactory;

    protected $table = 'lista_compras';

    protected $fillable = [
        'material',
        'metragem',
        'fornecedor',
        'situacao',
        'pedido_id', 'arquivado'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function estoque()
{
    return $this->belongsTo(Estoque::class, 'produto_id', 'produto_id');
}
}
