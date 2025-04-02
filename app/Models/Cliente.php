<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';  // Nome da tabela de clientes
    protected $primaryKey = 'id';  // Chave primária

    protected $fillable = [
        'nome', 'telefone', 'endereco', 'email', 'cpf'
    ];
}
