<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estoque extends Model
{
    use HasFactory;

     protected $table = 'estoque'; // Nome real da tabela
    protected $fillable = [
        'produto_id',
        'quantidade_disponivel',
        'localizacao',
        'nivel_medio',
        'quantidade_minima',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function movimentos()
    {
        return $this->hasMany(MovimentoEstoque::class);
    }
}
