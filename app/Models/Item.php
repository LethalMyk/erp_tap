<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    // Defina os campos que podem ser preenchidos (mass assignment)
    protected $fillable = [
        'pedido_id', // Adicione esta linha
        'nome_item',
        'material',
        'metragem',
        'especificacao',
    ];

    // Defina se o modelo usará timestamps automaticamente
    public $timestamps = true;

    public function pedido()
{
    return $this->belongsTo(Pedido::class, 'pedido_id', 'pedido_id');
}

}
