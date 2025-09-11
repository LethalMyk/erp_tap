<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProdutoComprado;
use App\Models\Estoque;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'unidade_medida',
        'categoria',
        'sub_categoria', // adiciona subcategoria
        'descricao',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Retorna todos os registros de produtos comprados relacionados a este produto
     */
    public function produtosComprados()
    {
        return $this->hasMany(ProdutoComprado::class, 'produto_id');
    }

    /**
     * Retorna o estoque vinculado a este produto
     */
    public function estoque()
    {
        return $this->hasOne(Estoque::class, 'produto_id');
    }

    /**
     * Retorna a quantidade disponível do produto no estoque, calculada pelos movimentos
     */
    public function quantidadeDisponivel()
    {
        if ($this->estoque) {
            return $this->estoque->movimentos()->sum('quantidade');
        }

        return 0;
    }

    /**
     * Retorna a categoria completa: Categoria / Subcategoria
     */
    public function categoriaCompleta()
    {
        return $this->categoria . ($this->sub_categoria ? ' / ' . $this->sub_categoria : '');
    }
}
