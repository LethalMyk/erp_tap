<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\ProdutoComprado;
use App\Models\Estoque;
use App\Models\MovimentoEstoque;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProdutoCompradoService
{
    /**
     * Registra ou atualiza um produto comprado e atualiza o estoque
     *
     * @param array $data Estrutura esperada:
     *   [
     *       'despesa_id' => int,
     *       'produto_id' => int|null,
     *       'nome_produto' => string|null,
     *       'quantidade' => float,
     *       'unidade_medida' => string,
     *       'valor_unitario' => float,
     *       'categoria' => string|null,
     *       'sub_categoria' => string|null,
     *       'obs' => string|null,
     *   ]
     */
    public function registrarProduto(array $data): ProdutoComprado
    {
        return DB::transaction(function () use ($data) {
            // 1. Criar ou buscar o produto
            if (!empty($data['nome_produto'])) {
                $produto = Produto::firstOrCreate(
                    ['nome' => $data['nome_produto']],
                    [
                        'unidade_medida' => $data['unidade_medida'] ?? 'UN',
                        'categoria' => $data['categoria'] ?? 'GERAL',
                        'subcategoria' => $data['sub_categoria'] ?? null,
                        'descricao' => $data['obs'] ?? null,
                    ]
                );
            } elseif (!empty($data['produto_id'])) {
                $produto = Produto::findOrFail($data['produto_id']);
            } else {
                throw new \InvalidArgumentException('Produto ou nome do produto é obrigatório.');
            }

            // 2. Criar ou atualizar ProdutoComprado
            $produtoComprado = ProdutoComprado::firstOrNew([
                'despesa_id' => $data['despesa_id'],
                'produto_id' => $produto->id,
            ]);

            $produtoComprado->quantidade = ($produtoComprado->quantidade ?? 0) + $data['quantidade'];
            $produtoComprado->unidade_medida = $data['unidade_medida'] ?? 'UN';
            $produtoComprado->valor_unitario = $data['valor_unitario'] ?? 0;
            $produtoComprado->valor_total = ($produtoComprado->quantidade * $produtoComprado->valor_unitario);
            $produtoComprado->obs = $data['obs'] ?? null;
            $produtoComprado->sub_categoria = $data['sub_categoria'] ?? null;
            $produtoComprado->save();

            // 3. Atualizar estoque
            $estoque = Estoque::firstOrCreate(
                ['produto_id' => $produto->id],
                ['nivel_medio' => 0, 'quantidade_minima' => 0, 'quantidade_disponivel' => 0]
            );

            // Registrar movimento de estoque
            MovimentoEstoque::create([
                'tipo' => 'ENTRADA',
                'estoque_id' => $estoque->id,
                'quantidade' => $data['quantidade'],
                'vinculo' => 'Despesa ID ' . $data['despesa_id'],
                'usuario_id' => Auth::id(),
                'data_movimento' => now(),
                'obs' => $data['obs'] ?? null,
            ]);

            return $produtoComprado;
        });
    }

    /**
     * Remove um produto comprado e ajusta o estoque
     *
     * @param ProdutoComprado $produtoComprado
     */
    public function removerProduto(ProdutoComprado $produtoComprado): void
    {
        DB::transaction(function () use ($produtoComprado) {
            // Ajusta o estoque
            $estoque = $produtoComprado->estoque;
            if ($estoque) {
                MovimentoEstoque::create([
                    'tipo' => 'SAIDA',
                    'estoque_id' => $estoque->id,
                    'quantidade' => $produtoComprado->quantidade,
                    'vinculo' => 'Remoção ProdutoComprado ID ' . $produtoComprado->id,
                    'usuario_id' => Auth::id(),
                    'data_movimento' => now(),
                    'obs' => 'Remoção de produto da despesa',
                ]);
            }

            // Remove produto comprado
            $produtoComprado->delete();
        });
    }
}
