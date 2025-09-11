<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produto;
use App\Models\MovimentoEstoque;

class Estoque extends Model
{
    use HasFactory;

    protected $table = 'estoque'; // Nome da tabela real

    protected $fillable = [
        'produto_id',
        'quantidade_disponivel',
        'nivel_medio',
        'quantidade_minima',
    ];

    protected $casts = [
        'quantidade_disponivel' => 'decimal:2',
        'nivel_medio' => 'decimal:2',
        'quantidade_minima' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com o produto
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    /**
     * Relacionamento com os movimentos de estoque
     */
    public function movimentos()
    {
        return $this->hasMany(MovimentoEstoque::class);
    }

    /**
     * Retorna a quantidade disponível atual
     */
    public function quantidadeDisponivel(): float
    {
        return $this->quantidade_disponivel ?? 0;
    }

    /**
     * Adiciona quantidade ao estoque e registra movimento
     */
    public function adicionar(float $quantidade, string $vinculo = null, string $obs = null)
    {
        if ($quantidade <= 0) {
            throw new \InvalidArgumentException('A quantidade deve ser maior que zero.');
        }

        $this->quantidade_disponivel += $quantidade;
        $this->save();

        MovimentoEstoque::create([
            'estoque_id' => $this->id,
            'quantidade' => $quantidade,
            'tipo' => 'entrada',
            'vinculo' => $vinculo,
            'usuario_id' => auth()->id(),
            'data_movimento' => now(),
            'obs' => $obs,
        ]);
    }

    /**
     * Remove quantidade do estoque e registra movimento
     */
    public function remover(float $quantidade, string $vinculo = null, string $obs = null)
    {
        if ($quantidade <= 0) {
            throw new \InvalidArgumentException('A quantidade deve ser maior que zero.');
        }

        if ($quantidade > $this->quantidadeDisponivel()) {
            throw new \InvalidArgumentException('Quantidade insuficiente no estoque.');
        }

        $this->quantidade_disponivel -= $quantidade;
        $this->save();

        MovimentoEstoque::create([
            'estoque_id' => $this->id,
            'quantidade' => $quantidade,
            'tipo' => 'saida',
            'vinculo' => $vinculo,
            'usuario_id' => auth()->id(),
            'data_movimento' => now(),
            'obs' => $obs,
        ]);
    }
}
