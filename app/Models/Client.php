<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clientes';  // Nome da tabela de clientes
    protected $primaryKey = 'client_id';  // Chave primária

    protected $fillable = [
        'nome', 'telefone', 'endereco', 'email', 'cpf'
    ];

    // Relacionamento com Pedido
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'client_id', 'client_id');
    }
}
