<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    protected $fillable = [
        'tipo',
        'data',
        'horario',
        'nome_cliente',
        'endereco',
        'itens',
        'observacao',
        'status',
        'telefone',
    
    ];
}
