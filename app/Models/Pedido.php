<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

protected $fillable = [
    'cliente_id', 'qntItens', 'data', 'valor', 'status', 'obs', 'prazo', 'imagem'
];


   public function cliente()
{
    return $this->belongsTo(Cliente::class, 'cliente_id');
}
public function items()
{
    return $this->hasMany(Item::class);
}

  public function imagens()
{
    return $this->hasMany(PedidoImagem::class);
}
  
}
