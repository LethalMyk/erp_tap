<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terceirizada extends Model
{
    use HasFactory;

    protected $fillable = ['tipoServico', 'obs', 'item_id', 'pedido_id'];

 public function pedido()
{
    return $this->belongsTo(Pedido::class);
}


public function item()
{
    return $this->belongsTo(Item::class, 'item_id');
}


public function cliente()
{
    return $this->hasOneThrough(Cliente::class, Pedido::class, 'id', 'id', 'pedido_id', 'cliente_id');
}

public function edit($id)
{
    $terceirizada = Terceirizada::findOrFail($id);
    
    // Certifique-se de carregar os pedidos com os itens relacionados
    $pedidos = Pedido::with('items')->get();

    return view('terceirizadas.edit', compact('terceirizada', 'pedidos'));
}
}
