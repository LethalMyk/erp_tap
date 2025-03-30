<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImagemPedido extends Model
{
    protected $table = 'imagens'; // Define o nome correto da tabela
    protected $fillable = ['pedido_id', 'imagem_path'];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
