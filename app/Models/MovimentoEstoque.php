<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimentoEstoque extends Model
{
    use HasFactory;


         protected $table = 'movimento_estoque'; // Nome real da tabela

    protected $fillable = [
        'tipo',
        'estoque_id',
        'quantidade',
        'vinculo',
        'usuario_id',
        'data_movimento',
        'obs',
    ];

    public function estoque()
    {
        return $this->belongsTo(Estoque::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
