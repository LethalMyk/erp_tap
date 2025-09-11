<?php

namespace App\Http\Controllers;

use App\Models\Despesa;
use App\Models\Parcela;
use App\Models\Produto;
use App\Models\ProdutoComprado;
use App\Models\Estoque;
use App\Models\MovimentoEstoque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class DespesaController extends Controller
{
    public function index(Request $request)
    {
        $query = Despesa::with('usuario', 'parcelas', 'produtosComprados.produto');

        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        if ($request->filled('forma_pagamento')) {
            $query->where('forma_pagamento', $request->forma_pagamento);
        }

        $despesas = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        return view('despesas.index', compact('despesas'));
    }

    public function create()
    {
        $produtos = Produto::orderBy('nome')->get();
        return view('despesas.create', compact('produtos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|date',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'categoria' => 'required|in:FORNECEDOR,AGUA,LUZ,MATERIAL,PARTICULAR,OUTROS',
            'forma_pagamento' => 'required|in:À VISTA,A PRAZO',
            'parcelas_descricao' => 'nullable|array',
            'parcelas_valor' => 'nullable|array',
            'parcelas_forma_pagamento' => 'nullable|array',
            'data_vencimento' => 'nullable|array',
            'chave_pagamento' => 'nullable|array',
            'comprovante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'observacao' => 'nullable|string',

            // Produtos
            'produtos_id' => 'nullable|array',
            'produtos_quantidade' => 'nullable|array',
            'produtos_unidade_medida' => 'nullable|array',
            'produtos_valor_unitario' => 'nullable|array',
            'produtos_valor_total' => 'nullable|array',
            'produtos_obs' => 'nullable|array',
            'produtos_novo' => 'nullable|array',
            'produtos_categoria' => 'nullable|array',
        ]);

        $comprovantePath = $request->hasFile('comprovante')
            ? $request->file('comprovante')->store('comprovantes', 'public')
            : null;

        DB::transaction(function () use ($validated, $request, $comprovantePath) {

            $despesa = Despesa::create([
                'descricao' => $validated['descricao'],
                'valor_total' => $validated['valor'],
                'categoria' => $validated['categoria'],
                'forma_pagamento' => $validated['forma_pagamento'],
                'observacao' => $validated['observacao'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Parcelas
            if ($validated['forma_pagamento'] === 'À VISTA') {
                Parcela::create([
                    'despesa_id' => $despesa->id,
                    'numero_parcela' => 1,
                    'valor_parcela' => $validated['valor'],
                    'data_vencimento' => $validated['data'],
                    'status' => 'PAGO',
                    'chave_pagamento' => Arr::get($request->chave_pagamento, 0),
                    'forma_pagamento' => Arr::get($request->parcelas_forma_pagamento, 0, 'PIX'),
                    'comprovante' => $comprovantePath,
                ]);
            } else {
                $parcelasDesc = $request->parcelas_descricao ?? [$validated['descricao']];
                $parcelasValor = $request->parcelas_valor ?? [$validated['valor']];
                $parcelasForma = $request->parcelas_forma_pagamento ?? [];
                $datas = $request->data_vencimento ?? [null];
                $chaves = $request->chave_pagamento ?? [null];

                foreach ($parcelasDesc as $index => $desc) {
                    Parcela::create([
                        'despesa_id' => $despesa->id,
                        'numero_parcela' => $index + 1,
                        'valor_parcela' => $parcelasValor[$index] ?? $validated['valor'],
                        'data_vencimento' => $datas[$index] ?? null,
                        'status' => 'PENDENTE',
                        'chave_pagamento' => $chaves[$index] ?? null,
                        'forma_pagamento' => $parcelasForma[$index] ?? 'PIX',
                        'comprovante' => $comprovantePath,
                    ]);
                }
            }

            // Produtos e estoque
            $produtosCount = max(
                count($request->produtos_id ?? []),
                count($request->produtos_novo ?? []),
                count($request->produtos_quantidade ?? [])
            );

            for ($i = 0; $i < $produtosCount; $i++) {
                $quantidade = (float) Arr::get($request->produtos_quantidade, $i, 0);
                if ($quantidade <= 0) continue;

                $nomeProduto = Arr::get($request->produtos_novo, $i);
                $idProdutoExistente = Arr::get($request->produtos_id, $i);

                if ($nomeProduto) {
                    $produto = Produto::firstOrCreate(
                        ['nome' => $nomeProduto],
                        [
                            'unidade_medida' => Arr::get($request->produtos_unidade_medida, $i, 'UN'),
                            'categoria' => Arr::get($request->produtos_categoria, $i, 'GERAL'),
                            'descricao' => Arr::get($request->produtos_obs, $i),
                        ]
                    );
                } elseif ($idProdutoExistente) {
                    $produto = Produto::find($idProdutoExistente);
                } else {
                    continue;
                }

                // Atualiza ou cria ProdutoComprado
                $produtoComprado = ProdutoComprado::firstOrNew([
                    'despesa_id' => $despesa->id,
                    'produto_id' => $produto->id,
                ]);
                $produtoComprado->quantidade = ($produtoComprado->quantidade ?? 0) + $quantidade;
                $produtoComprado->unidade_medida = Arr::get($request->produtos_unidade_medida, $i, 'UN');
                $produtoComprado->valor_unitario = Arr::get($request->produtos_valor_unitario, $i, 0);
                $produtoComprado->valor_total = ($produtoComprado->valor_total ?? 0) + Arr::get($request->produtos_valor_total, $i, 0);
                $produtoComprado->obs = Arr::get($request->produtos_obs, $i);
                $produtoComprado->save();

                $estoque = Estoque::firstOrCreate(
                    ['produto_id' => $produto->id],
                    ['nivel_medio' => 0, 'quantidade_minima' => 0]
                );

                MovimentoEstoque::create([
                    'tipo' => 'ENTRADA',
                    'estoque_id' => $estoque->id,
                    'quantidade' => $quantidade,
                    'vinculo' => 'Despesa ID ' . $despesa->id,
                    'usuario_id' => Auth::id(),
                    'data_movimento' => now(),
                    'obs' => Arr::get($request->produtos_obs, $i),
                ]);
            }
        });

        return redirect()->route('despesas.index')->with('success', 'Despesa cadastrada com sucesso!');
    }

    public function edit(Despesa $despesa)
    {
        $despesa->load('parcelas', 'produtosComprados.produto');
        $produtos = Produto::orderBy('nome')->get();
        return view('despesas.edit', compact('despesa', 'produtos'));
    }

    public function update(Request $request, Despesa $despesa)
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'categoria' => 'required|in:FORNECEDOR,AGUA,LUZ,MATERIAL,PARTICULAR,OUTROS',
            'forma_pagamento' => 'required|in:À VISTA,A PRAZO',
            'observacao' => 'nullable|string',
            'comprovante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('comprovante')) {
            foreach ($despesa->parcelas as $parcela) {
                if ($parcela->comprovante) {
                    Storage::disk('public')->delete($parcela->comprovante);
                }
                $parcela->update([
                    'comprovante' => $request->file('comprovante')->store('comprovantes', 'public')
                ]);
            }
        }

        $despesa->update($validated);

        // Produtos existentes ou novos
        $produtosCount = max(
            count($request->produtos_id ?? []),
            count($request->produtos_novo ?? []),
            count($request->produtos_quantidade ?? [])
        );

        for ($i = 0; $i < $produtosCount; $i++) {
            $quantidade = (float) Arr::get($request->produtos_quantidade, $i, 0);
            if ($quantidade <= 0) continue;

            $nomeProduto = Arr::get($request->produtos_novo, $i);
            $idProdutoExistente = Arr::get($request->produtos_id, $i);

            if ($nomeProduto) {
                $produto = Produto::firstOrCreate(
                    ['nome' => $nomeProduto],
                    [
                        'unidade_medida' => Arr::get($request->produtos_unidade_medida, $i, 'UN'),
                        'categoria' => Arr::get($request->produtos_categoria, $i, 'GERAL'),
                        'descricao' => Arr::get($request->produtos_obs, $i),
                    ]
                );
            } elseif ($idProdutoExistente) {
                $produto = Produto::find($idProdutoExistente);
            } else {
                continue;
            }

            $produtoComprado = ProdutoComprado::firstOrNew([
                'despesa_id' => $despesa->id,
                'produto_id' => $produto->id,
            ]);
            $produtoComprado->quantidade = ($produtoComprado->quantidade ?? 0) + $quantidade;
            $produtoComprado->unidade_medida = Arr::get($request->produtos_unidade_medida, $i, 'UN');
            $produtoComprado->valor_unitario = Arr::get($request->produtos_valor_unitario, $i, 0);
            $produtoComprado->valor_total = ($produtoComprado->valor_total ?? 0) + Arr::get($request->produtos_valor_total, $i, 0);
            $produtoComprado->obs = Arr::get($request->produtos_obs, $i);
            $produtoComprado->save();

            $estoque = Estoque::firstOrCreate(
                ['produto_id' => $produto->id],
                ['nivel_medio' => 0, 'quantidade_minima' => 0]
            );

            MovimentoEstoque::create([
                'tipo' => 'ENTRADA',
                'estoque_id' => $estoque->id,
                'quantidade' => $quantidade,
                'vinculo' => 'Despesa ID ' . $despesa->id,
                'usuario_id' => Auth::id(),
                'data_movimento' => now(),
                'obs' => Arr::get($request->produtos_obs, $i),
            ]);
        }

        return redirect()->route('despesas.index')->with('success', 'Despesa atualizada com sucesso!');
    }

    public function registrarPagamento(Request $request, $id)
    {
        $parcela = Parcela::findOrFail($id);

        $parcela->data_pagamento = $request->data_pagamento;
        $parcela->descricao = $request->descricao ?? $parcela->descricao;
        $parcela->status = 'PAGO';

        if ($request->hasFile('comprovante')) {
            $parcela->comprovante = $request->file('comprovante')->store('comprovantes', 'public');
        }

        $parcela->save();

        return response()->json(['success' => true]);
    }

    public function destroy(Despesa $despesa)
    {
        DB::transaction(function () use ($despesa) {
            foreach ($despesa->parcelas as $parcela) {
                if ($parcela->comprovante) {
                    Storage::disk('public')->delete($parcela->comprovante);
                }
                $parcela->delete();
            }

            foreach ($despesa->produtosComprados as $pc) {
                $pc->delete();
            }

            $despesa->delete();
        });

        return redirect()->route('despesas.index')->with('success', 'Despesa excluída com sucesso!');
    }
}
