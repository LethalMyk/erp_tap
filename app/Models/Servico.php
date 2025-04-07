<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

    protected $table = 'servicos';

    protected $fillable = [
        'profissional_id',
        'pedido_id',
        'codigo_servico',
        'data_inicio',
        'data_termino',
        'dificuldade',
        'data_previsao',
        'obs'
    ];

    // Relacionamento com Profissional
    public function profissional()
    {
        return $this->belongsTo(Profissional::class);
    }

    // Relacionamento com Pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
