<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produto;
use App\Models\MovimentoEstoque;

class Estoque extends Model
{
    use HasFactory;

    protected $table = 'estoque'; // Nome real da tabela

    protected $fillable = [
        'produto_id',
        'localizacao',
        'nivel_medio',
        'quantidade_minima',
    ];

    /**
     * Produto vinculado a este estoque
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    /**
     * Movimentos de entrada/saída deste estoque
     */
    public function movimentos()
    {
        return $this->hasMany(MovimentoEstoque::class);
    }

    /**
     * Calcula a quantidade disponível no estoque
     */
    public function quantidadeDisponivel(): int
    {
        // Soma todos os movimentos (entrada positiva, saída negativa)
        return $this->movimentos->sum(function ($mov) {
            return $mov->tipo === 'saida' ? -$mov->quantidade : $mov->quantidade;
        });
    }

    /**
     * Adiciona quantidade ao estoque
     *
     * @param int $quantidade
     * @param string $descricao
     * @return MovimentoEstoque
     */
    public function adicionar(int $quantidade, string $descricao = 'Entrada de estoque'): MovimentoEstoque
    {
        return $this->movimentos()->create([
            'quantidade' => $quantidade,
            'tipo' => 'entrada',
            'descricao' => $descricao,
            'usuario_id' => auth()->id(),
            'data_movimento' => now(),
        ]);
    }

    /**
     * Remove quantidade do estoque
     *
     * @param int $quantidade
     * @param string $descricao
     * @return MovimentoEstoque
     * @throws \Exception se estoque insuficiente
     */
    public function remover(int $quantidade, string $descricao = 'Saída de estoque'): MovimentoEstoque
    {
        if ($quantidade > $this->quantidadeDisponivel()) {
            throw new \Exception('Estoque insuficiente para esta operação.');
        }

        return $this->movimentos()->create([
            'quantidade' => $quantidade,
            'tipo' => 'saida',
            'descricao' => $descricao,
            'usuario_id' => auth()->id(),
            'data_movimento' => now(),
        ]);
    }
}
