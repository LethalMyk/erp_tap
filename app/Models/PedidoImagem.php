<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoImagem extends Model
{
    protected $table = 'pedido_imagens'; // Nome correto da tabela
    protected $fillable = ['pedido_id', 'imagem'];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}

