<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terceirizada extends Model
{
    use HasFactory;

    protected $fillable = ['tipoServico', 'obs', 'item_id', 'pedido_id'];

    // Relacionamento com Pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // Relacionamento com Item
  public function item()
{
    return $this->belongsTo(Item::class, 'item_id', 'id');
}


    // Relacionamento indireto com Cliente através do Pedido
    public function cliente()
    {
        return $this->hasOneThrough(
            Cliente::class,
            Pedido::class,
            'id',         // Chave primária de Pedido
            'id',         // Chave primária de Cliente
            'pedido_id',  // Chave estrangeira em Terceirizada
            'cliente_id'  // Chave estrangeira em Pedido
        );
    }
}
