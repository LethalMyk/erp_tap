<?php

namespace App\Services;

use App\Repositories\DespesaRepository;
use App\Repositories\ProdutoRepository;
use App\Models\Despesa;
use App\Models\ProdutoComprado;
use App\Services\MovimentoEstoqueService;
use App\Models\Estoque;

class DespesaService
{
    protected DespesaRepository $despesaRepo;
    protected ProdutoRepository $produtoRepo;

    public function __construct(DespesaRepository $despesaRepo, ProdutoRepository $produtoRepo)
    {
        $this->despesaRepo = $despesaRepo;
        $this->produtoRepo = $produtoRepo;
    }

    /**
     * Cria uma despesa com produtos (existentes ou novos)
     */
    public function criarDespesaComProdutos(array $data, ?string $comprovantePath = null): Despesa
    {
        $despesa = $this->despesaRepo->create([
            'data' => $data['data'],
            'descricao' => $data['descricao'],
            'valor_total' => $data['valor_total'] ?? 0,
            'categoria' => $data['categoria'],
            'forma_pagamento' => $data['forma_pagamento'],
            'observacao' => $data['observacao'] ?? null,
            'comprovante' => $comprovantePath,
            'created_by' => $data['created_by'] ?? null,
        ]);

        $this->associarProdutos($despesa, $data);

        return $despesa;
    }

    /**
     * Atualiza uma despesa
     */
    public function atualizarDespesa(array $data, Despesa $despesa, ?string $comprovantePath = null): void
    {
        $data['comprovante'] = $comprovantePath ?? $despesa->comprovante;
        $data['valor_total'] = $data['valor_total'] ?? $despesa->valor_total;

        $this->despesaRepo->update($despesa, $data);

        if (!empty($data['produtos_id']) || !empty($data['produtos_novo'])) {
            foreach ($despesa->produtosComprados as $produtoComprado) {
                $estoque = Estoque::where('produto_id', $produtoComprado->produto_id)->first();
                if ($estoque) {
                    app(MovimentoEstoqueService::class)->registrarMovimento(
                        $estoque,
                        -$produtoComprado->quantidade,
                        "Atualização despesa ID {$despesa->id}",
                        "Remoção produto antigo"
                    );
                }
            }
            $despesa->produtosComprados()->delete();
            $this->associarProdutos($despesa, $data);
        }
    }

    /**
     * Associa produtos à despesa e registra movimentos de estoque
     */
    protected function associarProdutos(Despesa $despesa, array $data): void
    {
        $movimentoService = app(MovimentoEstoqueService::class);
        $produtosId = $data['produtos_id'] ?? [];
        $produtosNovo = $data['produtos_novo'] ?? [];
        $quantidades = $data['produtos_quantidade'] ?? [];
        $valoresUnitarios = $data['produtos_valor_unitario'] ?? [];
        $valoresTotal = $data['produtos_valor_total'] ?? [];
        $categorias = $data['produtos_categoria'] ?? [];
        $subCategorias = $data['produtos_sub_categoria'] ?? [];
        $unidades = $data['produtos_unidade_medida'] ?? [];
        $observacoes = $data['produtos_obs'] ?? []; // <-- Captura as observações do formulário

        foreach ($produtosId as $i => $id) {
            $produto = null;

            if (!empty($id)) {
                $produto = $this->produtoRepo->find($id);
            } elseif (!empty($produtosNovo[$i])) {
                $produto = $this->produtoRepo->findByName($produtosNovo[$i]);
                if (!$produto) {
                    $produto = $this->produtoRepo->create([
                        'nome' => $produtosNovo[$i],
                        'categoria' => $categorias[$i] ?? null,
                        'sub_categoria' => $subCategorias[$i] ?? null,
                        'unidade_medida' => $unidades[$i] ?? null,
                    ]);
                }
            }

            if ($produto) {
                $estoque = Estoque::firstOrCreate(
                    ['produto_id' => $produto->id],
                    ['quantidade_disponivel' => 0, 'nivel_medio' => 0, 'quantidade_minima' => 0]
                );

                if (!empty($quantidades[$i])) {
                    $movimentoService->registrarMovimento(
                        $estoque,
                        $quantidades[$i],
                        "Despesa ID {$despesa->id}",
                        "Produto comprado"
                    );
                }

                ProdutoComprado::create([
                    'despesa_id' => $despesa->id,
                    'produto_id' => $produto->id,
                    'quantidade' => $quantidades[$i] ?? 0,
                    'valor_unitario' => $valoresUnitarios[$i] ?? 0,
                    'valor_total' => $valoresTotal[$i] ?? 0,
                    'obs' => $observacoes[$i] ?? null, // <-- Observação registrada aqui
                ]);
            }
        }
    }

    /**
     * Registra pagamento de parcela
     */
    public function registrarPagamentoParcela($parcela, array $data): void
    {
        $parcela->update($data);
    }

    /**
     * Exclui despesa e produtos associados
     */
    public function excluirDespesa(Despesa $despesa): void
    {
        foreach ($despesa->produtosComprados as $produtoComprado) {
            $estoque = Estoque::where('produto_id', $produtoComprado->produto_id)->first();
            if ($estoque) {
                app(MovimentoEstoqueService::class)->registrarMovimento(
                    $estoque,
                    -$produtoComprado->quantidade,
                    "Despesa ID {$despesa->id}",
                    "Exclusão de produto"
                );
            }
        }

        $despesa->produtosComprados()->delete();
        $this->despesaRepo->delete($despesa);
    }
}
