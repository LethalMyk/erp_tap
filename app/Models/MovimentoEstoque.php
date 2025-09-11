<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Estoque;
use App\Models\User;

class MovimentoEstoque extends Model
{
    use HasFactory;

    protected $table = 'movimento_estoque'; // Nome real da tabela

    protected $fillable = [
        'tipo',           // 'entrada' ou 'saida'
        'estoque_id',     // Estoque vinculado
        'quantidade',     // Quantidade do movimento (positiva ou negativa)
        'vinculo',        // Pode ser Pedido, Compra ou outro registro
        'usuario_id',     // Usuário que realizou o movimento
        'data_movimento', // Data do movimento
        'obs',            // Observações adicionais
    ];

    protected $casts = [
        'data_movimento' => 'datetime',
    ];

    /**
     * Estoque vinculado a este movimento
     */
    public function estoque()
    {
        return $this->belongsTo(Estoque::class);
    }

    /**
     * Usuário que realizou o movimento
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Retorna se o movimento é de entrada
     */
    public function isEntrada(): bool
    {
        return $this->tipo === 'entrada';
    }

    /**
     * Retorna se o movimento é de saída
     */
    public function isSaida(): bool
    {
        return $this->tipo === 'saida';
    }
}
