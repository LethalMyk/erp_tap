<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

protected $fillable = [
    'cliente_id', 'qntItens', 'data', 'valor', 'status', 'obs', 'prazo'
];


   public function cliente()
{
    return $this->belongsTo(Cliente::class, 'cliente_id');
}

    
}
