<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    // Defina o nome correto da tabela no banco de dados
    protected $table = 'clientes'; // Tabela no banco de dados
  // Caso o nome do campo de chave primária não seja "id"
    protected $primaryKey = 'client_id';  // Altere para o nome correto, se necessário
    
    protected $fillable = [
        'nome', 'telefone', 'endereco', 'email', 'cpf'
    ];
}
