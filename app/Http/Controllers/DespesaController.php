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

        $despesas = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->all());

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
            'forma_pagamento' => 'required|in:À VISTA,A PRAZO', // forma da despesa
            'parcelas_descricao' => 'nullable|array',
            'parcelas_valor' => 'nullable|array',
            'parcelas_forma_pagamento' => 'nullable|array', // forma de cada parcela
            'data_vencimento' => 'nullable|array',
            'chave_pagamento' => 'nullable|array',
            'comprovante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'observacao' => 'nullable|string',

            // Produtos comprados
            'produtos_id' => 'nullable|array',
            'produtos_quantidade' => 'nullable|array',
            'produtos_unidade_medida' => 'nullable|array',
            'produtos_valor_unitario' => 'nullable|array',
            'produtos_valor_total' => 'nullable|array',
            'produtos_obs' => 'nullable|array',
            'produtos_novo' => 'nullable|array',
            'produtos_categoria' => 'nullable|array',
        ]);

        $comprovantePath = null;
        if ($request->hasFile('comprovante')) {
            $comprovantePath = $request->file('comprovante')->store('comprovantes', 'public');
        }

        DB::transaction(function() use ($validated, $request, $comprovantePath) {
            $nota = Despesa::create([
                'descricao' => $validated['descricao'],
                'valor_total' => $validated['valor'],
                'categoria' => $validated['categoria'],
                'forma_pagamento' => $validated['forma_pagamento'], // à vista / a prazo
                'observacao' => $validated['observacao'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Parcelas
            if ($validated['forma_pagamento'] === 'À VISTA') {
                // Parcela única
                Parcela::create([
                    'despesa_id' => $nota->id,
                    'numero_parcela' => 1,
                    'valor_parcela' => $validated['valor'],
                    'data_vencimento' => $validated['data'],
                    'status' => 'PAGO',
                    'chave_pagamento' => $request->parcelas_chave_pagamento[0] ?? null,
                    'forma_pagamento' => $request->parcelas_forma_pagamento[0] ?? 'PIX', // default PIX
                    'comprovante' => $comprovantePath,
                ]);
            } else {
                // A PRAZO: múltiplas parcelas
                $parcelasDesc = $request->parcelas_descricao ?? [$validated['descricao']];
                $parcelasValor = $request->parcelas_valor ?? [$validated['valor']];
                $parcelasForma = $request->parcelas_forma_pagamento ?? [];
                $datas = $request->data_vencimento ?? [null];
                $chaves = $request->chave_pagamento ?? [null];

                foreach ($parcelasDesc as $index => $desc) {
                    Parcela::create([
                        'despesa_id' => $nota->id,
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

            // Produtos comprados + estoque
            $produtosCount = max(count($request->produtos_id ?? []), count($request->produtos_novo ?? []));
            for ($i = 0; $i < $produtosCount; $i++) {
                $nomeProduto = $request->produtos_novo[$i] ?? null;
                $categoria = $request->produtos_categoria[$i] ?? 'GERAL';
                $unidade = $request->produtos_unidade_medida[$i] ?? 'UN';

                if (!$nomeProduto) {
                    $produtoId = $request->produtos_id[$i] ?? null;
                    $produto = Produto::find($produtoId);
                    if (!$produto) continue;
                } else {
                    $produto = Produto::firstOrCreate(
                        ['nome' => $nomeProduto],
                        [
                            'unidade_medida' => $unidade,
                            'categoria' => $categoria,
                            'descricao' => $request->produtos_obs[$i] ?? null
                        ]
                    );
                }

                ProdutoComprado::create([
                    'despesa_id' => $nota->id,
                    'produto_id' => $produto->id,
                    'quantidade' => $request->produtos_quantidade[$i] ?? 0,
                    'unidade_medida' => $unidade,
                    'valor_unitario' => $request->produtos_valor_unitario[$i] ?? 0,
                    'valor_total' => $request->produtos_valor_total[$i] ?? 0,
                    'obs' => $request->produtos_obs[$i] ?? null,
                    'categoria' => $categoria,
                    'nome' => $nomeProduto ?? $produto->nome,
                ]);

                $estoque = Estoque::firstOrCreate(
                    ['produto_id' => $produto->id],
                    ['quantidade_disponivel' => 0, 'nivel_medio' => 0, 'quantidade_minima' => 0]
                );
                $estoque->increment('quantidade_disponivel', $request->produtos_quantidade[$i] ?? 0);

                MovimentoEstoque::create([
                    'tipo' => 'ENTRADA',
                    'estoque_id' => $estoque->id,
                    'quantidade' => $request->produtos_quantidade[$i] ?? 0,
                    'vinculo' => 'Despesa ID '.$nota->id,
                    'usuario_id' => Auth::id(),
                    'data_movimento' => now(),
                    'obs' => $request->produtos_obs[$i] ?? null,
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
                    'comprovante' => $request->file('comprovante')->store('comprovantes','public')
                ]);
            }
        }

        $despesa->update($validated);

        return redirect()->route('despesas.index')->with('success', 'Despesa atualizada com sucesso!');
    }

    public function registrarPagamento(Request $request, $id)
    {
        $parcela = Parcela::findOrFail($id);

        $parcela->data_pagamento = $request->data_pagamento;
        $parcela->descricao = $request->descricao ?? $parcela->descricao;
        $parcela->status = 'PAGO';

        if($request->hasFile('comprovante')){
            $parcela->comprovante = $request->file('comprovante')->store('comprovantes', 'public');
        }

        $parcela->save();

        return response()->json(['success' => true]);
    }

    public function destroy(Despesa $despesa)
    {
        DB::transaction(function() use ($despesa) {
            foreach ($despesa->parcelas as $parcela) {
                if ($parcela->comprovante) {
                    Storage::disk('public')->delete($parcela->comprovante);
                }
                $parcela->delete();
            }

            foreach ($despesa->produtosComprados as $pc) {
                $estoque = Estoque::where('produto_id', $pc->produto_id)->first();
                if ($estoque) {
                    $estoque->decrement('quantidade_disponivel', $pc->quantidade);
                }
                $pc->delete();
            }

            $despesa->delete();
        });

        return redirect()->route('despesas.index')->with('success', 'Despesa excluída com sucesso!');
    }
}
