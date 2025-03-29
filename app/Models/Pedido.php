<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    // Definir a chave primária se for diferente do padrão (ID)
    protected $primaryKey = 'pedido_id'; // Caso o nome da chave primária não seja 'id'

    // Definir os campos que podem ser preenchidos (mass assignment)
    protected $fillable = [
        'client_id', 
        'data', 
        'orcamento', 
        'status', 
        'prazo', 
        'data_retirada', 
        'obs'
    ];
public function pagamento()
{
    return $this->hasOne(Pagamento::class, 'pedido_id');
}

    // Definir se o modelo usará timestamps automaticamente
    public $timestamps = true;
}
