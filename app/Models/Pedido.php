<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pedido;
use App\Models\Servico;

class Pedido extends Model
{
    use HasFactory;

protected $fillable = [
    'cliente_id', 'qntItens', 'data', 'valor', 'status', 'obs', 'prazo', 'imagem', 'valorResta'
];


   public function cliente()
{
    return $this->belongsTo(Cliente::class, 'cliente_id');
}
public function items()
{
    return $this->hasMany(Item::class, 'pedido_id', 'id');
}
 

  public function imagens()
{
    return $this->hasMany(PedidoImagem::class);
}
  
   public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }
   public function servico()
{
    return $this->hasOne(Servico::class);
}
}
