<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    protected $fillable = [
        'tipo',
        'data',
        'horario',
        'nome_cliente',
        'endereco',
        'itens',
        'observacao',
        'status',
        'telefone',
        'pedido_id', // novo campo
    ];

    /**
     * Relacionamento: um agendamento pertence a um pedido
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
