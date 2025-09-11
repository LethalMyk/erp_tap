<?php

namespace App\Services;

use App\Repositories\EstoqueRepository;
use App\Models\Produto;
use App\Models\Estoque;
use App\Models\MovimentoEstoque;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class EstoqueService
{
    protected $repository;

    public function __construct(EstoqueRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Lista todos os produtos disponíveis no estoque
     */
    public function listarProdutos()
    {
        return $this->repository->allDisponiveis();
    }

    /**
     * Cria um novo produto
     */
    public function criarProduto(array $data): Produto
    {
        $validator = Validator::make($data, [
            'nome' => 'required|string|max:255',
            'quantidade_disponivel' => 'nullable|integer|min:0',
            'unidade_medida' => 'required|string|max:50',
            'categoria' => 'nullable|string|max:100',
            'sub_categoria' => 'nullable|string|max:100',
            'descricao' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            throw new \InvalidArgumentException($validator->errors()->first());
        }

        return Produto::create($validator->validated());
    }

    /**
     * Atualiza a quantidade disponível de um produto no estoque
     */
    public function atualizarQuantidade(int $produtoId, int $quantidade, string $vinculo = null): Estoque
    {
        if ($quantidade < 0) {
            throw new \InvalidArgumentException('A quantidade não pode ser negativa.');
        }

        $estoque = Estoque::firstOrCreate(['produto_id' => $produtoId]);

        // Calcula diferença
        $quantidadeAtual = $estoque->quantidadeDisponivel();
        $diferenca = $quantidade - $quantidadeAtual;

        if ($diferenca === 0) {
            return $estoque;
        }

        $tipo = $diferenca > 0 ? 'entrada' : 'saida';

        MovimentoEstoque::create([
            'estoque_id' => $estoque->id,
            'quantidade' => abs($diferenca),
            'tipo' => $tipo,
            'vinculo' => $vinculo,
            'usuario_id' => Auth::id(),
            'data_movimento' => now(),
            'obs' => $vinculo,
        ]);

        // Atualiza quantidade no estoque
        $estoque->quantidade_disponivel += $diferenca;
        $estoque->save();

        return $estoque;
    }

    /**
     * Cria ou recupera produto e estoque, e adiciona quantidade inicial
     */
    public function criarOuAtualizarProduto(array $data): Estoque
    {
        // Recupera ou cria produto
        if (!empty($data['produto_existente'])) {
            $produto = Produto::findOrFail($data['produto_existente']);
        } else {
            $produto = $this->criarProduto($data);
        }

        // Recupera ou cria estoque
        $estoque = Estoque::firstOrCreate(['produto_id' => $produto->id]);

        // Adiciona quantidade inicial
        $quantidadeInicial = $data['quantidade_inicial'] ?? 0;
        if ($quantidadeInicial > 0) {
            $this->atualizarQuantidade($produto->id, $estoque->quantidadeDisponivel() + $quantidadeInicial, 'Quantidade inicial');
        }

        return $estoque;
    }
}
